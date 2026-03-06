<?php

namespace App\Http\Controllers\DMS;

/* use App\Http\Controllers\Controller; */

use App\Models\DmsAgrCotisation;
use App\Models\DmsBudgetsAgr;
use App\Models\DmsCotisationMembre;
use Illuminate\Http\Request;
use App\Models\DmsMembre;
use App\Models\DmsServicesAgr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class AGRController extends VoyagerBaseController
{

    public function index(Request $request)
    {



        $annee = Carbon::now()->year;
        if ($request->annee) {
            $annee = $request->annee;
        }

        $CA = DmsAgrCotisation::CA_annuelle($annee);

        $taux_realisation = DmsBudgetsAgr::getTauxRealisationAnnuel($annee);

        $top = DmsAgrCotisation::topPerformanceByYear($annee);
        $stats = DmsAgrCotisation::getChiffreAffaireParActivite($annee);

        $cotisations = DmsAgrCotisation::where('annee', $annee)
            ->get();

        view()->share([
            'cotisations' => $cotisations,
            'stats' => $stats
        ]);
        view()->share('taux_realisation', $taux_realisation);
        view()->share('annee', $annee);
        view()->share('ca', $CA);
        view()->share('top', $top);

        return parent::index($request);
    }
    /**
     * Afficher le formulaire de création
     */
    public function create(Request $request)
    {
        $this->authorize('add', app("App\Models\DmsAgrCotisation"));

        $cotisations = DmsAgrCotisation::get();

        return view('voyager::dms-agr-cotisations.add', compact('cotisations'));
    }

    /**
     * Enregistrer une nouvelle cotisation
     */
    public function store(Request $request)
    {
        try {

            DB::beginTransaction();

            // ================================
            // 1️⃣ VALIDATION
            // ================================

            $validated = $request->validate([
                'evenement_id'       => 'required|exists:dms_evenements_agrs,id',
                'activite_id'        => 'required|exists:dms_activite_agrs,id',
                'service_id'         => 'required|exists:dms_services_agrs,id',

                'date_paiement'      => 'required|date|before_or_equal:today',
                'annee'              => 'required|integer|min:2022',

                'type_contributeur'  => 'required|in:membre,non_membre',
                'type_tarif'         => 'required|in:normal,special',

                'quantite'           => 'required|integer|min:1',

                'membre_id'          => 'required_if:type_contributeur,membre|nullable|exists:dms_membres,id',
                'nom_complet'        => 'required_if:type_contributeur,non_membre|nullable|string|max:255',
                'organisation'       => 'nullable|string|max:255',
                'email'              => 'nullable|email',
                'telephone'          => 'nullable|string|max:30',
                'pays_id'            => 'nullable|exists:pays,id',

                'observations'       => 'nullable|string|max:500',
            ]);

            // ================================
            // 2️⃣ RÉCUPÉRATION SERVICE
            // ================================

            $service = DmsServicesAgr::findOrFail($validated['service_id']);

            $quantite = max(1, (int) $validated['quantite']);

            // ================================
            // 3️⃣ DÉTERMINER PRIX NORMAL (BACKEND)
            // ================================

            $prixNormal = $service->tarif_non_membre;
            $isUpTodate = false;
            if ($validated['type_contributeur'] === 'membre') {

                $cotisation = DmsCotisationMembre::where('membre_id', $validated['membre_id'])->first();

                if ($cotisation && $cotisation->date_echeance && Carbon::parse($cotisation->date_echeance)->isFuture()) {
                    $prixNormal = $service->tarif_membre;
                    $isUpTodate = true;
                }
            }

            // ================================
            // 4️⃣ GESTION TARIF NORMAL / SPECIAL
            // ================================

            if ($validated['type_tarif'] === 'normal') {

                $prixUnitaire = $prixNormal;
                $montantTotal = $prixNormal * $quantite;
            } else {

                // Tarif spécial → on prend prix_unitaire envoyé
                $prixUnitaire = max(0, (float) $request->prix_unitaire);
                $montantTotal = $prixUnitaire * $quantite;
            }

            // ================================
            // 5️⃣ GESTION MEMBRE / NON MEMBRE
            // ================================

            if ($validated['type_contributeur'] === 'membre') {

                $membre = DmsMembre::findOrFail($validated['membre_id']);

                $validated['nom_complet'] = null;
                $validated['organisation'] = null;
                $validated['email'] = null;
                $validated['telephone'] = null;
                $validated['pays_id'] = null;
            } else {

                $validated['membre_id'] = null;
            }

            // ================================
            // 6️⃣ ENREGISTREMENT
            // ================================

            $cotisation = DmsAgrCotisation::create([

                'evenement_id'     => $validated['evenement_id'],
                'activite_id'      => $validated['activite_id'],
                'service_id'       => $validated['service_id'],

                'date_paiement'    => $validated['date_paiement'],
                'annee'            => $validated['annee'],

                'type_contributeur' => $validated['type_contributeur'],
                'membre_id'        => $validated['membre_id'] ?? null,
                'nom_complet'      => $validated['nom_complet'] ?? null,
                'organisation'     => $validated['organisation'] ?? null,
                'email'            => $validated['email'] ?? null,
                'telephone'        => $validated['telephone'] ?? null,
                'pays_id'          => $validated['pays_id'] ?? null,

                'quantite'         => $quantite,
                'prix_unitaire'    => $prixUnitaire,
                'montant_normal'   => $prixNormal,
                'montant_total'    => $montantTotal,
                'member_is_up_to_date'    => $isUpTodate,
                'type_tarif'       => $validated['type_tarif'],
                'observations'     => $validated['observations'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cotisation enregistrée avec succès',
                'data'    => $cotisation->load('service', 'membre', 'activite', 'Evenement')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur : ' . $e->getMessage()
            ], 500);
        }
    }


    public function edit(Request $request, $id)
    {
        $cotisation = DmsAgrCotisation::with(['evenement', 'activite', 'service', 'membre', 'pays'])->findOrFail($id);

        return view('vendor.voyager.dms-agr-cotisations.edit', compact('cotisation'));
    }

    public function update(Request $request, $id)
    {
        try {
            $cotisation = DmsAgrCotisation::findOrFail($id);

            $validated = $request->validate([
                'evenement_id' => 'required|exists:dms_evenements_agrs,id',
                'activite_id' => 'required|exists:dms_activite_agrs,id',
                'service_id' => 'required|exists:dms_services_agrs,id',
                'type_contributeur' => 'required|in:membre,non_membre',
                'type_tarif' => 'required|in:normal,special',
                'membre_id' => 'required_if:type_contributeur,membre|nullable|exists:dms_membres,id',
                'nom_complet' => 'required_if:type_contributeur,non_membre|nullable|string|max:255',
                'organisation' => 'nullable|string|max:255',
                'pays_id' => 'nullable|exists:pays,id',
                'email' => 'nullable|email|max:255',
                'telephone' => 'nullable|string|max:30',
                'quantite' => 'required|integer|min:1',
                'prix_unitaire' => 'nullable|numeric|min:0',
                'montant' => 'required|numeric|min:1',
                'date_paiement' => 'required|date|before_or_equal:today',
                'annee' => 'required|integer|min:2022|max:' . (date('Y') + 1),
                'observations' => 'nullable|string|max:500',
            ]);

            // Calcul du montant normal si nécessaire
            $service = DmsServicesAgr::find($validated['service_id']);
            $montant_normal = 0;

            if ($service) {
                // Logique pour déterminer le montant normal selon le type
                // À adapter selon votre structure de données
                $montant_normal = $validated['prix_unitaire'] ?? 0;
            }

            // Préparation des données
            $data = [
                'evenement_id' => $validated['evenement_id'],
                'activite_id' => $validated['activite_id'],
                'service_id' => $validated['service_id'],
                'type_contributeur' => $validated['type_contributeur'],
                'type_tarif' => $validated['type_tarif'],
                'quantite' => $validated['quantite'],
                'prix_unitaire' => $validated['prix_unitaire'] ?? 0,
                'montant_total' => $validated['montant'],
                'montant_normal' => $montant_normal,
                'date_paiement' => $validated['date_paiement'],
                'annee' => $validated['annee'],
                'observations' => $validated['observations'] ?? null,
            ];

            // Gestion des contributeurs
            if ($validated['type_contributeur'] === 'membre') {
                $membre = DmsMembre::find($validated['membre_id']);
                $data['membre_id'] = $membre->id;
                $data['membre'] = $membre->libelle_membre;
                $data['nom_complet'] = null;
                $data['organisation'] = null;
                $data['pays_id'] = null;
                $data['email'] = null;
                $data['telephone'] = null;
            } else {
                $data['membre_id'] = null;
                $data['membre'] = $validated['nom_complet'];
                $data['nom_complet'] = $validated['nom_complet'];
                $data['organisation'] = $validated['organisation'] ?? null;
                $data['pays_id'] = $validated['pays_id'] ?? null;
                $data['email'] = $validated['email'] ?? null;
                $data['telephone'] = $validated['telephone'] ?? null;
            }

            $cotisation->update($data);

            return redirect()
                ->route('voyager.dms-agr-cotisations.index')
                ->with('success', 'Cotisation modifiée avec succès');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une cotisation
     */
    public function destroy(Request $request, $id)
    {
        try {
            $cotisation = DmsAgrCotisation::findOrFail($id);
            $cotisation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cotisation supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ], 500);
        }
    }

    public function AmountAGR(Request $request)
    {
        Log::info($request->all());

        $member_id = $request->member_id;
        $service_id = $request->service_id;

        Log::info("Membre: $member_id, Service: $service_id");

        // Récupérer la visite
        $service = DmsServicesAgr::find($service_id);

        if (!$service) {
            return response()->json([
                'price' => 0,
                'error' => 'Service non trouvée'
            ], 404);
        }

        // Par défaut, prix non membre
        $price = $service->tarif_non_membre;

        // Si member_id est fourni et n'est pas 0 (non-membre)
        if ($member_id && $member_id != 0) {
            $membre = DmsCotisationMembre::where('membre_id', $member_id)->first();

            // Vérifier si le membre existe et a une date d'échéance valide
            if ($membre && $membre->date_echeance) {
                if (Carbon::parse($membre->date_echeance)->isFuture()) {
                    $price = $service->tarif_membre;
                }
            }
        }

        return response()->json([
            'price' => $price
        ]);
    }

    public function chartData(Request $request)
    {
        $annee = $request->annee ?? date('Y');

        $data = DmsAgrCotisation::select(
            'dms_activite_agrs.libelle_activite as activite',
            DB::raw('MONTH(dms_agr_cotisations.date_paiement) as mois'),
            DB::raw('SUM(dms_agr_cotisations.montant_total) as total')
        )
            ->join(
                'dms_activite_agrs',
                'dms_agr_cotisations.activite_id',
                '=',
                'dms_activite_agrs.id'
            )
            ->whereYear('dms_agr_cotisations.date_paiement', $annee)
            ->groupBy(
                'dms_activite_agrs.libelle_activite',
                DB::raw('MONTH(dms_agr_cotisations.date_paiement)')
            )
            ->orderBy('mois')
            ->get();

        return response()->json($data);
    }

    public function byActivite($id)
    {
        return DmsServicesAgr::where('activite_id', $id)
            ->where('statuts', 'Active')
            ->get();
    }
}
