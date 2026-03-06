<?php

namespace App\Http\Controllers\DMS;

use App\Exports\DMS\MembresAJourExport;
use App\Exports\DMS\MembresNonAJourExport;
use App\Http\Controllers\Controller;
use App\Models\DmsCotisationMembre;
use App\Models\DmsMembre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\DmsPlanAdhesion;

class ExportsDmsController extends Controller
{
    public function export(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'format' => 'required|in:excel,csv,pdf,xlsx',
                'search' => 'nullable|string',
                'year' => 'nullable|string'
            ]);

            // Construire la requête
            $query = DmsCotisationMembre::with(['membre', 'membre.plan_adhesion'])
                ->select('dms_cotisation_membres.*')
                ->join('dms_membres', 'dms_cotisation_membres.membre_id', '=', 'dms_membres.id')
                ->orderBy('dms_cotisation_membres.created_at', 'desc');

            // Appliquer les filtres
            if ($request->has('year') && $request->year !== 'all') {
                $query->where('annee_cotisation', $request->year);
            }

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

            // Récupérer les données
            $cotisations = $query->get();

            // Préparer les filtres
            $filters = [
                'year' => $request->year,
                'search' => $request->search
            ];

            // Générer le nom de fichier
            $fileName = 'cotisations_' . date('Y-m-d_His');

            // Exporter selon le format
            switch ($request->format) {
                case 'excel':
                case 'xlsx':
                    return Excel::download(
                        new MembresAJourExport($cotisations, $filters),
                        $fileName . '.xlsx'
                    );

                case 'csv':
                    return Excel::download(
                        new MembresAJourExport($cotisations, $filters),
                        $fileName . '.csv',
                        \Maatwebsite\Excel\Excel::CSV,
                        [
                            'Content-Type' => 'text/csv',
                        ]
                    );

                case 'pdf':
                    // Utiliser la vue PDF existante
                    $data = [
                        'cotisations' => $cotisations,
                        'filters' => [
                            'search' => $request->search,
                            'year' => $request->year !== 'all' ? $request->year : 'Toutes',
                            'export_date' => now()->format('d/m/Y H:i'),
                        ],
                        'total_montant' => $cotisations->sum('montant'),
                        'total_reste' => $cotisations->sum('reste_a_payer'),
                    ];

                    $pdf = Pdf::loadView('voyager::dms-cotisation-membres.export-pdf', $data);
                    return $pdf->download($fileName . '.pdf');
            }
        } catch (\Exception $e) {
            Log::error('Erreur export cotisations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'export : ' . $e->getMessage()
                ], 500);
            }

            return back()->with([
                'message' => 'Erreur lors de l\'export : ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    public function membresNonAJour(Request $request)
    {
        try {
            // Validation des paramètres
            $request->validate([
                'annee' => 'nullable|integer|min:2020|max:' . (date('Y') + 1),
                'format' => 'nullable|in:excel,csv,pdf',
                'retard_min' => 'nullable|integer|min:0'
            ]);

            $annee = $request->get('annee', date('Y'));
            $retardMin = $request->get('retard_min', 0);
            $dateReference = $request->get('date_reference')
                ? Carbon::parse($request->date_reference)
                : now();

            // Récupérer tous les membres avec leurs cotisations
            $membres = DmsMembre::with([
                'plan_adhesion',
                'cotisations' => function ($query) use ($annee) {
                    $query->where('annee_cotisation', $annee)
                        ->orderBy('date_paiement', 'desc');
                }
            ])->get();

            // Filtrer les membres non à jour
            $membresNonAJour = $membres->filter(function ($membre) use ($annee, $retardMin, $dateReference) {
                // Total payé pour l'année
                $totalPaye = $membre->cotisations->sum('montant');

                // Montant attendu selon le plan
                $montantAttendu = $membre->plan_adhesion->price_xof ?? 0;

                // Vérifier si le membre est à jour
                if ($totalPaye >= $montantAttendu) {
                    return false; // Membre à jour
                }

                // Si on veut filtrer par retard
                if ($retardMin > 0) {
                    $derniereCotisation = $membre->cotisations->first();
                    if ($derniereCotisation && $derniereCotisation->date_echeance) {
                        $joursRetard = Carbon::parse($derniereCotisation->date_echeance)
                            ->diffInDays($dateReference, false);

                        return $joursRetard >= $retardMin;
                    }
                    // Si pas de cotisation du tout, considérer comme en retard
                    return $totalPaye == 0;
                }

                return true; // Membre non à jour
            });

            // Trier par retard (les plus anciens d'abord)
            $membresNonAJour = $membresNonAJour->sortByDesc(function ($membre) use ($annee, $dateReference) {
                $derniereCotisation = $membre->cotisations->first();
                if ($derniereCotisation && $derniereCotisation->date_echeance) {
                    return Carbon::parse($derniereCotisation->date_echeance)
                        ->diffInDays($dateReference, false);
                }
                return 365; // Un an si pas de cotisation
            });

            // Répondre selon le format demandé
            if ($request->has('format')) {
                $fileName = 'membres_non_a_jour_' . $annee . '_' . date('Y-m-d_His');

                switch ($request->format) {
                    case 'excel':
                    case 'csv':
                        $export = new MembresNonAJourExport($membresNonAJour, $annee, $dateReference);

                        if ($request->format === 'csv') {
                            return Excel::download($export, $fileName . '.csv', \Maatwebsite\Excel\Excel::CSV);
                        }

                        return Excel::download($export, $fileName . '.xlsx');

                    case 'pdf':
                        $data = [
                            'membres' => $membresNonAJour,
                            'annee' => $annee,
                            'dateReference' => $dateReference,
                            'total' => $membresNonAJour->count(),
                            'dateExport' => now()->format('d/m/Y H:i'),
                            'retardMin' => $retardMin
                        ];

                        return Pdf::loadView('voyager::dms-cotisation-membres.non-a-jour-pdf', $data)
                            ->setPaper('A4', 'landscape')
                            ->download($fileName . '.pdf');
                }
            }

            // Retourner les données pour l'affichage
            return view('voyager::dms-cotisation-membres.membres-non-a-jour', [
                'membres' => $membresNonAJour,
                'annee' => $annee,
                'totalMembres' => $membres->count(),
                'totalNonAJour' => $membresNonAJour->count(),
                'retardMin' => $retardMin
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur export membres non à jour', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->with([
                'message' => 'Erreur: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    public function exportMembresNonAJour(Request $request)
    {
        try {
            // Validation des paramètres
            $validated = $request->validate([
                'annee' => 'required|integer|min:2020|max:' . (date('Y') + 1),
                'retard_min' => 'nullable|integer|min:0',
                'plan_id' => 'nullable|exists:dms_plan_adhesions,id',
                'statut' => 'nullable|in:non_cotise,partiel,retard',
                'include_contact' => 'nullable|boolean',
                'format' => 'required|in:excel,csv,pdf',
                'search' => 'nullable|string'
            ]);

            $annee = $validated['annee'];
            $retardMin = $validated['retard_min'] ?? 0;
            $planId = $validated['plan_id'] ?? null;
            $statutFilter = $validated['statut'] ?? null;
            $includeContact = $validated['include_contact'] ?? true;
            $format = $validated['format'];
            $search = $validated['search'] ?? null;

            // Date de référence pour le calcul du retard
            $dateReference = $request->has('date_reference')
                ? Carbon::parse($request->date_reference)
                : now();

            // Récupérer tous les membres avec leurs relations
            $query = DmsMembre::with([
                'plan_adhesion',
                'cotisations' => function ($q) use ($annee) {
                    $q->where('annee_cotisation', $annee)
                        ->orderBy('date_paiement', 'desc');
                }
            ]);

            // Filtrer par plan si spécifié
            if ($planId) {
                $query->where('plan_adhesion_id', $planId);
            }

            // Recherche par nom
            if ($search) {
                $query->where('libelle_membre', 'LIKE', "%{$search}%");
            }

            $membres = $query->get();

            // Filtrer les membres non à jour
            $membresNonAJour = $membres->filter(function ($membre) use ($annee, $retardMin, $statutFilter, $dateReference) {
                // Total payé pour l'année
                $totalPaye = $membre->cotisations->sum('montant');

                // Montant attendu selon le plan
                $montantAttendu = $membre->plan_adhesion->price_xof ?? 0;

                // Vérifier si le membre est à jour
                if ($totalPaye >= $montantAttendu) {
                    return false; // Membre à jour
                }

                // Calculer le retard
                $joursRetard = 0;
                $derniereCotisation = $membre->cotisations->first();
                if ($derniereCotisation && $derniereCotisation->date_echeance) {
                    $joursRetard = Carbon::parse($derniereCotisation->date_echeance)
                        ->diffInDays($dateReference, false);
                }

                // Appliquer le filtre de statut
                switch ($statutFilter) {
                    case 'non_cotise':
                        return $totalPaye == 0;

                    case 'partiel':
                        return $totalPaye > 0 && $totalPaye < $montantAttendu;

                    case 'retard':
                        return $joursRetard > 0;

                    default:
                        // Si filtre de retard minimum
                        if ($retardMin > 0) {
                            return $joursRetard >= $retardMin;
                        }
                        return true; // Tous les membres non à jour
                }
            });

            // Trier par retard (les plus anciens d'abord)
            $membresNonAJour = $membresNonAJour->sortByDesc(function ($membre) use ($annee, $dateReference) {
                $derniereCotisation = $membre->cotisations->first();
                if ($derniereCotisation && $derniereCotisation->date_echeance) {
                    return Carbon::parse($derniereCotisation->date_echeance)
                        ->diffInDays($dateReference, false);
                }
                return 365; // Un an si pas de cotisation
            });

            // Générer le nom de fichier
            $fileName = 'membres_non_a_jour_' . $annee;
            if ($retardMin > 0) {
                $fileName .= '_retard_' . $retardMin . '_jours';
            }
            if ($planId) {
                $plan = DmsPlanAdhesion::find($planId);
                $fileName .= '_' . str_slug($plan->title_plan);
            }
            $fileName .= '_' . date('Y-m-d_His');

            // Préparer les données de filtre pour l'export
            $filterData = [
                'annee' => $annee,
                'retard_min' => $retardMin,
                'plan_id' => $planId,
                'statut' => $statutFilter,
                'include_contact' => $includeContact,
                'date_reference' => $dateReference->format('Y-m-d'),
                'total_membres' => $membres->count(),
                'total_non_ajour' => $membresNonAJour->count()
            ];

            // Exporter selon le format
            switch ($format) {
                case 'excel':
                    $export = new MembresNonAJourExport($membresNonAJour, $filterData);
                    return Excel::download($export, $fileName . '.xlsx');

                case 'csv':
                    $export = new MembresNonAJourExport($membresNonAJour, $filterData);
                    return Excel::download($export, $fileName . '.csv', \Maatwebsite\Excel\Excel::CSV, [
                        'Content-Type' => 'text/csv',
                    ]);

                case 'pdf':
                    // Préparer les données pour le PDF
                    $pdfData = $this->preparePdfData($membresNonAJour, $filterData);

                    $pdf = Pdf::loadView('voyager::exports.membres-non-a-jour-pdf', $pdfData)
                        ->setPaper('A4', 'landscape')
                        ->setOptions([
                            'defaultFont' => 'DejaVu Sans',
                            'isHtml5ParserEnabled' => true,
                            'isRemoteEnabled' => true
                        ]);

                    return $pdf->download($fileName . '.pdf');

                default:
                    throw new \Exception('Format non supporté');
            }
        } catch (\Exception $e) {
            \Log::error('Erreur export membres non à jour', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'export : ' . $e->getMessage()
                ], 500);
            }

            return back()->with([
                'message' => 'Erreur lors de l\'export : ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    /**
     * Préparer les données pour le PDF
     */
    private function preparePdfData($membres, $filters)
    {
        $totalMontantRetard = 0;
        $nonCotises = 0;
        $partiels = 0;
        $enRetard = 0;

        $membresFormatted = $membres->map(function ($membre) use ($filters, &$totalMontantRetard, &$nonCotises, &$partiels, &$enRetard) {
            $totalPaye = $membre->cotisations->sum('montant');
            $montantAttendu = $membre->plan_adhesion->price_xof ?? 0;
            $resteAPayer = max(0, $montantAttendu - $totalPaye);

            $totalMontantRetard += $resteAPayer;

            // Statut
            if ($totalPaye == 0) {
                $statut = 'Non cotisé';
                $statutClass = 'danger';
                $nonCotises++;
            } elseif ($totalPaye < $montantAttendu) {
                $statut = 'Partiel';
                $statutClass = 'warning';
                $partiels++;
            } else {
                $statut = 'À jour';
                $statutClass = 'success';
            }

            // Calcul du retard
            $joursRetard = 0;
            $derniereCotisation = $membre->cotisations->first();
            if ($derniereCotisation && $derniereCotisation->date_echeance) {
                $joursRetard = Carbon::parse($derniereCotisation->date_echeance)
                    ->diffInDays(Carbon::parse($filters['date_reference']), false);

                if ($joursRetard > 0 && $resteAPayer > 0) {
                    $statut = 'En retard';
                    $statutClass = 'danger';
                    $enRetard++;
                }
            }

            return [
                'id' => $membre->id,
                'nom' => $membre->libelle_membre,
                'plan' => $membre->plan_adhesion->title_plan ?? 'N/A',
                'montant_attendu' => $montantAttendu,
                'montant_paye' => $totalPaye,
                'reste_a_payer' => $resteAPayer,
                'pourcentage_paye' => $montantAttendu > 0 ? round(($totalPaye / $montantAttendu) * 100) : 0,
                'statut' => $statut,
                'statut_class' => $statutClass,
                'jours_retard' => max(0, $joursRetard),
                'date_dernier_paiement' => $derniereCotisation ? $derniereCotisation->date_paiement->format('d/m/Y') : null,
                'telephone' => $filters['include_contact'] ? ($membre->telephone ?? '') : '',
                'email' => $filters['include_contact'] ? ($membre->email ?? '') : '',
                'date_adhesion' => $membre->created_at->format('d/m/Y'),
            ];
        });

        return [
            'membres' => $membresFormatted,
            'filters' => [
                'annee' => $filters['annee'],
                'retard_min' => $filters['retard_min'],
                'date_reference' => Carbon::parse($filters['date_reference'])->format('d/m/Y'),
                'plan' => isset($filters['plan_id']) ? DmsPlanAdhesion::find($filters['plan_id'])->title_plan ?? 'Tous' : 'Tous',
                'statut' => $this->getStatutLabel($filters['statut']),
            ],
            'statistiques' => [
                'total_membres' => $filters['total_membres'],
                'total_non_ajour' => $filters['total_non_ajour'],
                'non_cotises' => $nonCotises,
                'partiels' => $partiels,
                'en_retard' => $enRetard,
                'montant_retard_total' => $totalMontantRetard,
            ],
            'date_export' => now()->format('d/m/Y à H:i'),
            'generated_by' => auth()->user()->name ?? 'Système',
        ];
    }

    /**
     * Obtenir le libellé du statut
     */
    private function getStatutLabel($statut)
    {
        $labels = [
            'non_cotise' => 'Non cotisé',
            'partiel' => 'Partiel',
            'retard' => 'En retard',
            null => 'Tous'
        ];

        return $labels[$statut] ?? 'Tous';
    }

    
}
