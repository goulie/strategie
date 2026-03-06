<?php

namespace App\Http\Controllers\DMS;

use App\Exports\DMS\Members;
use App\Http\Controllers\Controller;
use App\Models\CotisationMembre;
use App\Models\DmsCotisationMembre;
use App\Models\DmsMembre;
use App\Models\DmsObjectifsMembre;
use App\Models\DmsPlanAdhesion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class MembresController extends Controller
{
    public function ListeByObjectif(Request $request)
    {
        $annee = $request->get('annee', now()->year);
        $search = $request->get('search');

        // Récupérer les données
        $membresParMois = DmsMembre::getMembresParMois($annee);
        $objectifsParMois = DmsObjectifsMembre::getObjectifsParAnnee($annee);

        // Préparer les données pour le graphique
        $dataChart = $this->preparerDonneesGraphique($membresParMois, $objectifsParMois, $annee);

        // Récupérer la liste des membres
        $membres = DmsMembre::getMembresAvecFiltres($annee, $search);

        // Années disponibles
        $anneesDisponibles = range(now()->year, now()->year - 5);


        return Voyager::view('voyager::dms-membres.listebyobjective', compact(
            'membres',
            'annee',
            'search',
            'dataChart',
            'anneesDisponibles',
            'objectifsParMois',
        ));
    }

    public function mettreAJourObjectif(Request $request)
    {
        $request->validate([
            'annee' => 'required|integer',
            'mois' => 'required|integer|between:1,12',
            'objectif' => 'required|integer|min:0'
        ]);

        $objectif = DmsObjectifsMembre::updateOrCreate(
            [
                'annee' => $request->annee,
                'mois' => $request->mois
            ],
            [
                'objectif' => $request->objectif,
                'description' => $request->description
            ]
        );

        return response()->json([
            'success' => true,
            'objectif' => $objectif
        ]);
    }

    private function preparerDonneesGraphique($membresParMois, $objectifsParMois, $annee)
    {
        $moisLabels = [];
        $donneesMembres = [];
        $donneesObjectifs = [];

        for ($mois = 1; $mois <= 12; $mois++) {
            $moisLabels[] = date('M', mktime(0, 0, 0, $mois, 1));

            // Membres réels
            $donneesMembres[] = isset($membresParMois[$mois])
                ? $membresParMois[$mois]->total
                : 0;

            // Objectifs
            $donneesObjectifs[] = isset($objectifsParMois[$mois])
                ? $objectifsParMois[$mois]->objectif
                : 0;
        }

        return [
            'labels' => $moisLabels,
            'membres' => $donneesMembres,
            'objectifs' => $donneesObjectifs,
            'annee' => $annee
        ];
    }

    public function exporterExcel(Request $request)
    {
        $annee = $request->get('annee', now()->year);
        $membres = DmsMembre::whereYear('date_adhesion', $annee)->get();

        return response()->streamDownload(function () use ($membres, $annee) {
            $handle = fopen('php://output', 'w');

            // En-têtes
            fputcsv($handle, [
                'Code Membre',
                'Nom',
                'Email',
                'Organisation',
                'Date Adhésion',
                'Plan Adhésion',
                'Ligne Budgétaire'
            ], ';');

            // Données
            foreach ($membres as $membre) {
                fputcsv($handle, [
                    $membre->code_membre,
                    $membre->libelle_membre,
                    $membre->email,
                    $membre->organisation,
                    $membre->date_adhesion->format('d/m/Y'),
                    $membre->plan_adhesion->libelle ?? '',
                    $membre->linge_budgetaire->libelle ?? ''
                ], ';');
            }

            fclose($handle);
        }, "membres_{$annee}.csv");
    }


    public function filterByPlan($typePlan, $annee, $planId = null)
    {
        $query = DmsMembre::query();

        // Filtre par type de plan d'adhésion
        $query->whereHas('plan_adhesion', function ($q) use ($typePlan) {
            $q->where('type_plan_adhesion', $typePlan);
        });

        // Si un plan spécifique est sélectionné
        if ($planId) {
            $query->where('plan_adhesion_id', $planId);
        }

        // Filtre pour n'avoir que les membres qui ont cotisé pour l'année sélectionnée
        $query->whereHas('cotisations', function ($q) use ($annee) {
            $q->where('annee_cotisation', $annee)
                ->whereDate('date_echeance', '>=', Carbon::now());
        });


        $typePlanLibelle = $this->getTypePlanLibelle($typePlan);
        $plan = $planId ? DmsPlanAdhesion::find($planId) : null;

        $membreIds = $query->pluck('id');

        $montantTotal = DmsCotisationMembre::whereIn('membre_id', $membreIds)
            ->where('annee_cotisation', $annee)
            ->sum('montant');

        $membres = $query->with(['plan_adhesion', 'cotisations' => function ($q) use ($annee) {
            $q->where('annee_cotisation', $annee);
        }])->orderBy('date_adhesion', 'desc')->get();

        return view('voyager::dms-cotisation-membres.cotisations.index', compact('membres', 'typePlan', 'typePlanLibelle', 'annee', 'plan', 'montantTotal'));
    }

    /**
     * Retourne le libellé du type de plan
     */
    private function getTypePlanLibelle($typePlan)
    {
        switch ($typePlan) {
            case DmsPlanAdhesion::PLAN_ADHESION_ACTIF:
                return 'MEMBRES ACTIFS';
            case DmsPlanAdhesion::PLAN_ADHESION_INDIVIDUEL:
                return 'MEMBRES INDIVIDUELS';
            case DmsPlanAdhesion::PLAN_ADHESION_AFFILIE:
                return 'MEMBRES AFFILIÉS';
            default:
                return 'MEMBRES';
        }
    }


    public function membresAJour($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        // Récupérer les IDs des membres à jour
        $membreIds = DmsCotisationMembre::where('annee_cotisation', $annee)
            ->whereDate('date_echeance', '>=', Carbon::today())
            ->distinct('membre_id')
            ->pluck('membre_id');

        $membres = DmsMembre::whereIn('id', $membreIds)
            ->with(['plan_adhesion', 'linge_budgetaire', 'pays', 'cotisations' => function ($q) use ($annee) {
                $q->where('annee_cotisation', $annee);
            }])
            ->orderBy('date_adhesion', 'desc')
            ->get();

        // Statistiques
        $stats = DmsCotisationMembre::membresAJourStats($annee);

        $typeStatut = 'AJOUR';
        $titre = "Membres à jour - $annee";
        $sousTitre = "Liste des membres ayant une cotisation valide pour l'année $annee";

        return view('voyager::dms-cotisation-membres.cotisations.liste-statut', compact('membres', 'annee', 'stats', 'typeStatut', 'titre', 'sousTitre'));
    }

    /**
     * Affiche la liste des membres non à jour
     */
    public function membresNonAJour($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;
        $today = Carbon::today();

        // Sous-requête pour avoir la dernière cotisation de chaque membre
        $subQuery = DmsCotisationMembre::selectRaw('MAX(id) as id')
            ->groupBy('membre_id');

        $cotisations = DmsCotisationMembre::whereIn('id', $subQuery)
            ->whereDate('date_echeance', '<', $today)
            ->get();

        $membreIds = $cotisations->pluck('membre_id');

        $membres = DmsMembre::whereIn('id', $membreIds)
            ->with(['plan_adhesion', 'linge_budgetaire', 'pays'])
            ->orderBy('date_adhesion', 'desc')
            ->get();

        // Ajouter la dernière cotisation à chaque membre
        foreach ($membres as $membre) {
            $membre->derniere_cotisation = $cotisations->firstWhere('membre_id', $membre->id);
        }

        // Statistiques
        $stats = DmsCotisationMembre::MembresNonAJour($annee);

        $typeStatut = 'NONAJOUR';
        $titre = "Membres non à jour - $annee";
        $sousTitre = "Liste des membres dont la cotisation a expiré pour l'année $annee";

        return view('voyager::dms-cotisation-membres.cotisations.liste-statut', compact('membres', 'annee', 'stats', 'typeStatut', 'titre', 'sousTitre'));
    }

    /**
     * Affiche la liste des membres expirés (plus de 3 ans sans cotisation)
     */
    public function membresExpires()
    {
        $membresData = DmsCotisationMembre::MembresExpires();

        // Convertir en collection de modèles DmsMembre
        $membreIds = collect($membresData)->pluck('id');

        $membres = DmsMembre::whereIn('id', $membreIds)
            ->with(['plan_adhesion', 'linge_budgetaire', 'pays'])
            ->orderBy('date_adhesion', 'desc')
            ->get();

        // Récupérer la dernière cotisation pour chaque membre
        foreach ($membres as $membre) {
            $membre->derniere_cotisation = DmsCotisationMembre::where('membre_id', $membre->id)
                ->orderBy('date_paiement', 'desc')
                ->first();
        }

        $typeStatut = 'EXPIRE';
        $titre = "Membres à réactiver";
        $sousTitre = "Membres n'ayant pas cotisé depuis plus de 3 ans";

        return view('voyager::dms-cotisation-membres.cotisations.liste-statut', compact('membres', 'typeStatut', 'titre', 'sousTitre'));
    }

    public function exportAll()
    {
        return Excel::download(new Members(), 'membres.xlsx');
    }
}
