<?php

namespace App\Http\Controllers\DMS;

use App\Http\Controllers\Controller;
use App\Imports\CotisationsImport;
use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Models\DmsCotisationMembre;
use App\Models\DmsMembre;
use App\Models\DmsPlanAdhesion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Pdf;

class CotisationController extends VoyagerBaseController
{
    public function index(Request $request)
    {

        // Vérifier la permission
        $this->authorize('browse', app("App\Models\DmsCotisationMembre"));

        $annee = $request->get('annee', Carbon::now()->year);

        $stats = DB::table('dms_plan_adhesions')
            ->join('dms_membres', 'dms_plan_adhesions.id', '=', 'dms_membres.plan_adhesion_id')
            ->join('dms_cotisation_membres', 'dms_membres.id', '=', 'dms_cotisation_membres.membre_id')
            ->where('dms_cotisation_membres.annee_cotisation', $annee)
            // ->where('dms_cotisation_membres.date_echeance', '>=', Carbon::now()->format('Y-m-d'))
            ->select(
                'dms_plan_adhesions.title_plan',
                DB::raw('COUNT(DISTINCT dms_membres.id) as nb_membres'),
                DB::raw('SUM(dms_cotisation_membres.montant) as montant_total')
            )
            ->groupBy('dms_plan_adhesions.title_plan')
            ->get();

        $cotisations = DmsCotisationMembre::PourAnnee($annee)->get();
        $membres = DmsMembre::all();
        view()->share('stats', $stats);
        view()->share('cotisations', $cotisations);
        view()->share('membres', $membres);
        //return parent::index($request);
        return view('voyager::dms-cotisation-membres.home', compact('cotisations', 'membres', 'stats', 'annee'));
    }

    public function create(Request $request)
    {
        // Récupérer le DataType
        $dataType = \TCG\Voyager\Models\DataType::where('slug', 'dms-cotisation-membres')->first();

        if (!$dataType) {
            abort(404, 'DataType not found');
        }

        // Vérifier la permission
        $this->authorize('add', app($dataType->model_name));

        // Créer une nouvelle instance
        $dataTypeContent = new DmsCotisationMembre();

        $dataTypeContent = new DmsCotisationMembre();
        $membres = DmsMembre::with('plan_adhesion')->get();

        $cotisations = DmsCotisationMembre::with('membre')
            ->orderBy('created_at', 'desc')
            ->get();

        view()->share(compact('dataTypeContent', 'membres', 'cotisations', 'dataType'));

        view()->share('edit', false);

        return view('voyager::dms-cotisation-membres.edit-add');
    }

    public function edit(Request $request, $id)
    {
        $dataTypeContent = DmsCotisationMembre::findOrFail($id);
        $membres = DmsMembre::with('plan_adhesion')->get();
        $cotisations = DmsCotisationMembre::with('membre')
            ->orderBy('created_at', 'desc')
            ->get();

        view()->share(compact('dataTypeContent', 'membres', 'cotisations'));
        view()->share('edit', true);

        return view('voyager::dms-cotisation-membres.edit-add');
    }


    public function store_cotisation(Request $request)
    {
        try {
            // Normaliser les données avant validation
            $request->merge([
                'type_versement' => strtolower($request->type_versement),
                'mode_paiement' => trim($request->mode_paiement),
            ]);

            // Validation stricte
            $validated = $request->validate([
                'membre_id'             => 'required|exists:dms_membres,id',
                'type_versement'        => 'required|in:total,partiel,TOTAL,PARTIEL',
                'montant'               => 'required|integer|min:1',
                'montant_total_attendu' => 'required|integer|min:1',
                'annee_cotisation'      => 'required|integer|min:2020|max:' . (date('Y') + 5),
                'date_paiement'         => 'required|date|before_or_equal:today',
                'mode_paiement'         => 'required|in:Cash,"En ligne",cheque,virement',
                'reference'             => 'nullable|string|max:100',
                'observation'           => 'nullable|string',
                'reste_a_payer'         => 'nullable|integer|min:0',
            ]);

            // Normaliser les valeurs après validation
            $validated['type_versement'] = strtoupper($validated['type_versement']);

            // Sécurité : recalcul côté serveur
            $montantTotal = (int) $validated['montant_total_attendu'];
            $montantPaye  = (int) $validated['montant'];

            if ($montantPaye > $montantTotal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le montant payé ne peut pas dépasser le montant total attendu.'
                ], 422);
            }

            // Calcul du reste (utiliser celui du formulaire ou recalculer)
            $reste = isset($validated['reste_a_payer'])
                ? (int) $validated['reste_a_payer']
                : $montantTotal - $montantPaye;

            // Statut basé sur le type_versement
            $status = strtoupper($validated['type_versement']); // TOTAL ou PARTIEL

            // Date d'échéance (31 mars année+1)
            $dateEcheance = Carbon::create(
                $validated['annee_cotisation'] + 1,
                3,
                31
            )->format('Y-m-d');

            // Vérifier si une cotisation existe déjà
            $existing = DmsCotisationMembre::where('membre_id', $validated['membre_id'])
                ->where('annee_cotisation', $validated['annee_cotisation'])
                ->where('status', '!=', 'annulé')
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une cotisation existe déjà pour ce membre pour l\'année ' . $validated['annee_cotisation']
                ], 409);
            }

            // Transaction DB
            DB::beginTransaction();

            $cotisation = DmsCotisationMembre::create([
                'membre_id'            => $validated['membre_id'],
                'montant'              => $montantPaye,
                'reste_a_payer'        => $reste,
                'mode_paiement'        => $validated['mode_paiement'],
                'date_paiement'        => $validated['date_paiement'],
                'annee_cotisation'     => $validated['annee_cotisation'],
                'date_echeance'        => $dateEcheance,
                'status'               => $status,
                'observation'          => $validated['observation']
                    ?? ($status === 'TOTAL'
                        ? 'Paiement total de la cotisation'
                        : 'Paiement partiel de la cotisation'),
                'user_id'              => auth()->id(),
                'reference'            => $validated['reference'] ?? null,
                'montant_total_attendu' => $montantTotal, // Champ manquant
            ]);

            DB::commit();

            // Réponse AJAX
            return response()->json([
                'success' => true,
                'message' => 'Cotisation enregistrée avec succès.',
                'data' => [
                    'id'            => $cotisation->id,
                    'membre'        => $cotisation->membre->libelle_membre ?? 'N/A',
                    'montant'       => $montantPaye,
                    'reste_a_payer' => $reste,
                    'status'        => $status,
                    'annee'         => $validated['annee_cotisation'],
                ],
                'redirect' => route('voyager.dms-cotisation-membres.create')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();

            \Log::error('Erreur store_cotisation', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la cotisation.',
                'error'   => config('app.debug') ? $th->getMessage() : null
            ], 500);
        }
    }

    public function update_cotisation(Request $request)
    {
        try {
            // Normaliser les données avant validation
            $request->merge([
                'type_versement' => strtolower($request->type_versement),
                'mode_paiement' => trim($request->mode_paiement),
            ]);

            // Validation stricte pour la mise à jour
            $validated = $request->validate([
                'id'                    => 'required|exists:dms_cotisation_membres,id',
                'type_versement'        => 'required|in:total,partiel,TOTAL,PARTIEL',
                'montant'               => 'required|integer|min:1',
                'montant_total_attendu' => 'required|integer|min:1',
                'annee_cotisation'      => 'required|integer|min:2020|max:' . (date('Y') + 5),
                'date_paiement'         => 'required|date|before_or_equal:today',
                'mode_paiement'         => 'required|in:Cash,"En ligne",cheque,virement',
                'reference'             => 'nullable|string|max:100',
                'observation'           => 'nullable|string',
                'reste_a_payer'         => 'nullable|integer|min:0',
            ]);

            // Récupérer la cotisation existante
            $cotisation = DmsCotisationMembre::findOrFail($validated['id']);

            // Vérifier que l'utilisateur a le droit de modifier
            // (optionnel : vérifier les permissions selon votre logique métier)

            // Normaliser les valeurs après validation
            $validated['type_versement'] = strtoupper($validated['type_versement']);

            // Sécurité : recalcul côté serveur
            $montantTotal = (int) $validated['montant_total_attendu'];
            $montantPaye  = (int) $validated['montant'];

            if ($montantPaye > $montantTotal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le montant payé ne peut pas dépasser le montant total attendu.'
                ], 422);
            }

            // Calcul du reste (utiliser celui du formulaire ou recalculer)
            $reste = isset($validated['reste_a_payer'])
                ? (int) $validated['reste_a_payer']
                : $montantTotal - $montantPaye;

            // Statut basé sur le type_versement
            $status = strtoupper($validated['type_versement']); // TOTAL ou PARTIEL

            // Date d'échéance (31 mars année+1)
            $dateEcheance = Carbon::create(
                $validated['annee_cotisation'] + 1,
                3,
                31
            )->format('Y-m-d');

            // Vérifier si une cotisation existe déjà pour un autre membre la même année
            // (Uniquement si on change l'année ou le membre)
            if ($cotisation->annee_cotisation != $validated['annee_cotisation']) {
                $existing = DmsCotisationMembre::where('membre_id', $cotisation->membre_id)
                    ->where('annee_cotisation', $validated['annee_cotisation'])
                    ->where('id', '!=', $cotisation->id)
                    ->where('status', '!=', 'annulé')
                    ->first();

                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Une cotisation existe déjà pour ce membre pour l\'année ' . $validated['annee_cotisation']
                    ], 409);
                }
            }

            // Transaction DB
            DB::beginTransaction();

            // Préparer les données de mise à jour
            $updateData = [
                'montant'              => $montantPaye,
                'reste_a_payer'        => $reste,
                'mode_paiement'        => $validated['mode_paiement'],
                'date_paiement'        => $validated['date_paiement'],
                'annee_cotisation'     => $validated['annee_cotisation'],
                'date_echeance'        => $dateEcheance,
                'status'               => $status,
                'reference'            => $validated['reference'] ?? $cotisation->reference,
                'montant_total_attendu' => $montantTotal,
                'updated_at'           => now(),
            ];

            // Gérer l'observation
            if (isset($validated['observation'])) {
                $updateData['observation'] = $validated['observation'];
            } elseif (!isset($cotisation->observation)) {
                // Si pas d'observation existante, ajouter une observation par défaut
                $updateData['observation'] = $status === 'TOTAL'
                    ? 'Paiement total de la cotisation (mis à jour)'
                    : 'Paiement partiel de la cotisation (mis à jour)';
            }

            // Mettre à jour la cotisation
            $cotisation->update($updateData);

            // Historique des modifications (optionnel)
            $this->logCotisationUpdate($cotisation, $updateData);

            DB::commit();

            // Réponse AJAX
            return response()->json([
                'success' => true,
                'message' => 'Cotisation mise à jour avec succès.',
                'data' => [
                    'id'            => $cotisation->id,
                    'membre'        => $cotisation->membre->libelle_membre ?? 'N/A',
                    'montant'       => $montantPaye,
                    'reste_a_payer' => $reste,
                    'status'        => $status,
                    'annee'         => $validated['annee_cotisation'],
                    'updated_at'    => $cotisation->updated_at->format('d/m/Y H:i'),
                ],
                'redirect' => route('voyager.dms-cotisation-membres.index')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cotisation non trouvée.'
            ], 404);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Erreur update_cotisation', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'request' => $request->all(),
                'cotisation_id' => $request->id ?? 'non défini'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la cotisation.',
                'error'   => config('app.debug') ? $th->getMessage() : null
            ], 500);
        }
    }

    /**
     * Journaliser les modifications (méthode optionnelle)
     */
    private function logCotisationUpdate($cotisation, $updateData)
    {
        // Vous pouvez utiliser un système de logs ou une table d'historique
        Log::info('Cotisation mise à jour', [
            'cotisation_id' => $cotisation->id,
            'membre_id' => $cotisation->membre_id,
            'modifications' => $updateData,
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    public function store(Request $request)
    {
        $anneeCotisation = $request->annee_cotisation;

        if (!$anneeCotisation) {
            // Par défaut, utiliser l'année en cours
            $anneeCotisation = now()->year;
            $request->merge(['annee_cotisation' => $anneeCotisation]);
        }

        // Date d'échéance : fin mars de l'année SUIVANTE l'année de cotisation
        $dateEcheance = Carbon::create($anneeCotisation + 1, 3, 31);

        // S'assurer que c'est bien le 31 mars
        if ($dateEcheance->month !== 3 || $dateEcheance->day !== 31) {
            $dateEcheance = Carbon::create($anneeCotisation + 1, 3, 31);
        }

        // Ajouter la date d'échéance calculée
        $request->merge([
            'date_echeance' => $dateEcheance->format('Y-m-d'),
        ]);

        // Vérifier si une cotisation existe déjà pour ce membre cette année
        $cotisationExistante = DmsCotisationMembre::where('membre_id', $request->membre_id)
            ->where('annee_cotisation', $anneeCotisation)
            ->first();

        if ($cotisationExistante) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Ce membre a déjà payé sa cotisation pour l\'année ' . $anneeCotisation,
                    'alert-type' => 'warning',
                ]);
        }

        // Appeler la méthode parent
        return parent::store($request);
    }

    /**
     * Export des membres à jour en Excel
     */
    public function exportExcel(Request $request)
    {
        $annee = $request->get('annee', Carbon::now()->year);
        $format = $request->get('format', 'xlsx');

        return Excel::download(new MembresAJourExport($annee), "membres-a-jour-{$annee}.{$format}");
    }

    /**
     * Export des membres à jour en PDF
     */
    public function exportPdf(Request $request)
    {
        $annee = $request->get('annee', Carbon::now()->year);

        // Récupérer les données
        $membres = $this->getMembresAJourData($annee);
        $statistiques = $this->getStatsExport($annee);

        $pdf = Pdf::loadView('exports.membres-a-jour-pdf', [
            'membres' => $membres,
            'annee' => $annee,
            'statistiques' => $statistiques,
            'dateExport' => Carbon::now()->format('d/m/Y H:i'),
        ]);

        return $pdf->download("membres-a-jour-{$annee}.pdf");
    }

    /**
     * Export des membres à jour en CSV
     */
    public function exportCsv(Request $request)
    {
        $annee = $request->get('annee', Carbon::now()->year);

        return Excel::download(new MembresAJourExport($annee), "membres-a-jour-{$annee}.csv", \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Vue d'export avec options
     */
    public function showExportForm(Request $request)
    {
        $annee = $request->get('annee', Carbon::now()->year);

        // Générer les années disponibles
        $currentYear = Carbon::now()->year;
        $anneesDisponibles = [];

        for ($i = 5; $i >= 0; $i--) {
            $year = $currentYear - $i;
            $anneesDisponibles[$year] = $year . ($i == 0 ? ' (Cette année)' : '');
        }

        return view('exports.export-form', [
            'anneeSelectionnee' => $annee,
            'anneesDisponibles' => $anneesDisponibles,
        ]);
    }

    /**
     * Méthode pour récupérer les données des membres à jour
     */
    private function getMembresAJourData($annee)
    {
        return DmsMembre::with(['cotisations' => function ($query) use ($annee) {
            $query->where('annee_cotisation', $annee)
                ->where('date_echeance', '>=', Carbon::now()->format('Y-m-d'))
                ->orderBy('date_paiement', 'desc');
        }])
            ->whereHas('cotisations', function ($query) use ($annee) {
                $query->where('annee_cotisation', $annee)
                    ->where('date_echeance', '>=', Carbon::now()->format('Y-m-d'));
            })
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
    }

    /**
     * Méthode pour les statistiques d'export
     */
    private function getStatsExport($annee)
    {
        return [
            'total_membres' => DmsMembre::count(),
            'membres_a_jour' => DmsCotisationMembre::membresAJour($annee),
            'membres_non_a_jour' => DmsCotisationMembre::membresNonAJour($annee),
            'taux_recouvrement' => DmsCotisationMembre::tauxRecouvrement($annee),
            'total_cotisations' => DmsCotisationMembre::totalCotisationsAnnuelles($annee),
            'nombre_cotisations' => DmsCotisationMembre::nbCotisations($annee),
            'nouveaux_membres' => DmsCotisationMembre::nouveauxMembresAnnuels($annee),
        ];
    }

    /**
     * Export détaillé avec toutes les cotisations
     */
    public function exportCotisationsDetail(Request $request)
    {
        $annee = $request->get('annee', Carbon::now()->year);

        $cotisations = DmsCotisationMembre::with('membre')
            ->where('annee_cotisation', $annee)
            ->where('date_echeance', '>=', Carbon::now()->format('Y-m-d'))
            ->orderBy('date_paiement')
            ->get();

        $pdf = Pdf::loadView('exports.cotisations-detail-pdf', [
            'cotisations' => $cotisations,
            'annee' => $annee,
            'totalMontant' => $cotisations->sum('montant'),
            'nombreCotisations' => $cotisations->count(),
            'dateExport' => Carbon::now()->format('d/m/Y H:i'),
        ]);

        return $pdf->download("cotisations-detaillees-{$annee}.pdf");
    }

    public function getMontantCotisation($membreId, Request $request)
    {
        try {
            $membre = DmsMembre::with('plan_adhesion')->find($membreId);

            if (!$membre) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membre non trouvé'
                ], 404);
            }

            // Récupérer le montant total du plan d'adhésion
            $montantTotal = $membre->plan_adhesion ? $membre->plan_adhesion->price_xof : 0;

            // Vérifier si le membre a déjà une cotisation pour l'année
            $annee = $request->get('annee') ?? date('Y');
            $cotisationExistante = DmsCotisationMembre::where('membre_id', $membreId)
                ->where('annee_cotisation', $annee)
                ->first();

            $data = [
                'montant_total' => (float) $montantTotal,
                'a_cotisation_existante' => false,
                'reste_a_payer' => 0,
                'montant_deja_paye' => 0,
                'cotisation_id' => null,
                'peut_payer_reste' => false
            ];

            if ($cotisationExistante) {
                $data['a_cotisation_existante'] = true;
                $data['reste_a_payer'] = $cotisationExistante->reste_a_payer;
                $data['montant_deja_paye'] = $cotisationExistante->montantTotalPaye();
                $data['cotisation_id'] = $cotisationExistante->id;
                $data['peut_payer_reste'] = $cotisationExistante->aResteAPayer();
            }

            Log::info($data);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du montant'
            ], 500);

            Log::error($e->getMessage());
        }
    }

    public function get_template()
    {
        return view('voyager::templateImport');
    }


    public function export_template(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            $import = new CotisationsImport();
            Excel::import($import, $request->file('fichier'));

            $results = $import->getResults();

            return response()->json([
                'success' => true,
                'message' => 'Importation terminée',
                'data' => [
                    'imported' => $results['imported'],
                    'failed' => $results['failed'],
                    'total' => $results['imported'] + $results['failed'],
                ],
                'errors' => $results['errors']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'importation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function template()
    {
        // Retourner un template vide pour le téléchargement
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_cotisations.csv"',
        ];

        $content = "Matricule,Annee,date_paiement\n";
        $content .= "MAFF122023-831,2022,N/A\n";
        $content .= "MACT112023-964,2022,N/A\n";
        $content .= "MACT112020-155,2022,02/08/2024\n";

        return response($content, 200, $headers);
    }

    public function loadCotisations(Request $request)
    {
        try {
            // Pagination
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Requête de base
            $query = DmsCotisationMembre::with(['membre.plan_adhesion'])
                ->select('dms_cotisation_membres.*')
                ->join('dms_membres', 'dms_cotisation_membres.membre_id', '=', 'dms_membres.id')
                ->orderBy('dms_cotisation_membres.created_at', 'desc');

            // Filtre par année
            if ($request->has('year') && $request->year !== 'all') {
                $query->where('annee_cotisation', $request->year);
            }

            // Recherche
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('dms_membres.libelle_membre', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('dms_cotisation_membres.reference', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('dms_cotisation_membres.montant', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('dms_cotisation_membres.mode_paiement', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('dms_cotisation_membres.observation', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Pagination
            $cotisations = $query->paginate($perPage, ['*'], 'page', $page);

            // Formater les données pour le JSON
            $formattedData = $cotisations->map(function ($cotisation) {
                return [
                    'id' => $cotisation->id,
                    'membre_libelle' => $cotisation->membre->libelle_membre ?? 'N/A',
                    'plan_adhesion' => $cotisation->membre->plan_adhesion->title_plan ?? 'N/A',
                    'annee_cotisation' => $cotisation->annee_cotisation,
                    'montant' => $cotisation->montant,
                    'reste_a_payer' => $cotisation->reste_a_payer,
                    'montant_total_attendu' => $cotisation->montant_total_attendu,
                    'status' => $cotisation->status,
                    'date_paiement' => $cotisation->date_paiement,
                    'date_echeance' => $cotisation->date_echeance,
                    'mode_paiement' => $cotisation->mode_paiement,
                    'reference' => $cotisation->reference,
                    'observation' => $cotisation->observation,
                    'created_at' => $cotisation->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'current_page' => $cotisations->currentPage(),
                'last_page' => $cotisations->lastPage(),
                'per_page' => $cotisations->perPage(),
                'total' => $cotisations->total(),
                'from' => $cotisations->firstItem(),
                'to' => $cotisations->lastItem(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des cotisations',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function getDetails($id)
    {
        try {
            // Récupérer la cotisation avec toutes les relations nécessaires
            $cotisation = DmsCotisationMembre::with([
                'membre',
                'membre.plan_adhesion',
                'user' // Si vous avez un champ user_id
            ])->findOrFail($id);

            // Formater les données pour la réponse
            $data = [
                'id' => $cotisation->id,
                'membre_id' => $cotisation->membre_id,
                'membre_libelle' => $cotisation->membre->libelle_membre ?? 'Non spécifié',
                'membre_email' => $cotisation->membre->email ?? null,
                'membre_telephone' => $cotisation->membre->telephone ?? null,
                'plan_adhesion' => $cotisation->membre->plan_adhesion->title_plan ?? 'Non spécifié',
                'plan_montant' => $cotisation->membre->plan_adhesion->price_xof ?? 0,
                'annee_cotisation' => $cotisation->annee_cotisation,
                'montant' => $cotisation->montant,
                'reste_a_payer' => $cotisation->reste_a_payer,
                'montant_total_attendu' => $cotisation->montant_total_attendu,
                'status' => $cotisation->status,
                'type_versement' => $cotisation->type_versement ?? $cotisation->status,
                'date_paiement' => $cotisation->date_paiement ? $cotisation->date_paiement->format('Y-m-d') : null,
                'date_echeance' => $cotisation->date_echeance ? $cotisation->date_echeance->format('Y-m-d') : null,
                'mode_paiement' => $cotisation->mode_paiement,
                'reference' => $cotisation->reference ?? 'Non spécifié',
                'observation' => $cotisation->observation,
                'created_at' => $cotisation->created_at->format('d/m/Y H:i'),
                'updated_at' => $cotisation->updated_at->format('d/m/Y H:i'),
                'enregistre_par' => $cotisation->user->name ?? 'Système',
            ];

            // Ajouter des informations calculées
            $data['pourcentage_paye'] = $cotisation->montant_total_attendu > 0
                ? round(($cotisation->montant / $cotisation->montant_total_attendu) * 100, 2)
                : 0;

            $data['jours_restants'] = null;
            if ($cotisation->date_echeance) {
                $echeance = Carbon::parse($cotisation->date_echeance);
                $now = Carbon::now();
                $data['jours_restants'] = $now->diffInDays($echeance, false); // Négatif si dépassé
            }

            // Historique des paiements si plusieurs paiements pour la même année
            $paiements = DmsCotisationMembre::where('membre_id', $cotisation->membre_id)
                ->where('annee_cotisation', $cotisation->annee_cotisation)
                ->orderBy('date_paiement', 'asc')
                ->get(['montant', 'date_paiement', 'mode_paiement', 'reference']);

            $data['historique_paiements'] = $paiements;

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cotisation non trouvée'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur getDetails', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails'
            ], 500);
        }
    }

    public function liste_membres_non_a_jour()
    {

        $lists = DmsCotisationMembre::MembresNonAJour(Date('Y'))['data'];

        return view('voyager::dms-cotisation-membres.partials.liste_non_a_jour', compact('lists'));
    }

    public function liste_membres_expired()
    {

        $lists = DmsCotisationMembre::MembresNonAJour(Date('Y'));

        return view('voyager::dms-cotisation-membres.partials.liste_expired', compact('lists'));
    }
}
