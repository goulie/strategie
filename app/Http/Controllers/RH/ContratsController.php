<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\RhContrat;
use App\Models\RhListePersonnel;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Voyager;

class ContratsController extends VoyagerBaseController
{
    public function index(Request $request)
    {
        try {

            return parent::index($request);
        } catch (\Exception $ex) {

            dd($ex->getMessage());
        }
    }

    public function create(Request $request)
    {
        $collaborateurs = RhListePersonnel::all();
        view()->share('collaborateurs', $collaborateurs);
        return parent::create($request);
    }

    //store
    public function store(Request $request)
    {
        $request->validate([
            'date_debut' => 'nullable|date',
            'duree' => 'nullable|integer|min:1',
            'collaborateur_id' => 'required|exists:rh_liste_personnels,id',
            'type_contrat' => 'required|in:CDI,CDD,INTERIM,STAGE,APPRENTISSAGE,INSERTION,FREELANCE',
            'duree' => 'nullable|required_if:type_contrat,CDD,INTERIM,FREELANCE,STAGE,APPRENTISSAGE,INSERTION|integer|min:1',
        ]);

        if (RhContrat::hasContratNonCloture($request->collaborateur_id)) {
            return redirect()->back()
                ->withErrors([
                    'collaborateur_id' =>
                    'Ce collaborateur possède déjà un contrat actif. Veuillez clôturer le contrat en cours.'
                ])
                ->withInput();
        }

        $dernierContrat = RhContrat::where('personel_id', $request->collaborateur_id)
            ->orderByDesc('date_debut')
            ->first();

        $renouvellement = false;

        if ($dernierContrat && $dernierContrat->type_contrat === $request->type_contrat) {
            $renouvellement = true;
        }

        if (
            $request->has('date_debut') && $request->filled('date_debut')
            && $request->has('duree') && $request->filled('duree')
        ) {

            try {
                $dateDebut = Carbon::parse($request->date_debut);
                $dureeMois = (int) $request->duree;

                // Ajouter les mois
                $dateFin = $dateDebut->copy()->addMonths($dureeMois);

                // Ajuster pour la fin de mois (si le jour n'existe pas dans le mois suivant)
                $jourOriginal = $dateDebut->day;
                $dernierJourMois = $dateFin->copy()->endOfMonth()->day;

                if ($jourOriginal > $dernierJourMois) {
                    $dateFin->day = $dernierJourMois;
                }

                $request->merge([
                    'date_fin' => $dateFin->format('Y-m-d'),
                    'personel_id' => $request->collaborateur_id,
                    'user_id' => auth()->user()->id,
                    'renouvellement' => $renouvellement,
                ]);
            } catch (\Exception $e) {
                // Log l'erreur mais continue
                Log::error('Erreur calcul date fin: ' . $e->getMessage());
            }
        }

        // Pour les contrats à durée indéterminée, mettre date_fin à null
        if ($request->type_contrat === RhContrat::CONTRAT_CDI) {
            $request->merge([
                'date_fin' => null,
                'duree' => null
            ]);
        }

        return parent::store($request);
    }

    public function edit(Request $request, $id)
    {
        $collaborateurs = RhListePersonnel::all();
        view()->share('collaborateurs', $collaborateurs);
        return parent::edit($request, $id);
    }

    public function update(Request $request, $id)
    {
        // Récupérer le contrat existant
        $contrat = \App\Models\RhContrat::findOrFail($id);

        // Appliquer les mêmes règles de validation que dans store()
        $request->validate([
            'date_debut' => 'nullable|date',
            'collaborateur_id' => 'required|exists:rh_liste_personnels,id',
            'type_contrat' => 'required|in:CDI,CDD,INTERIM,STAGE,APPRENTISSAGE,INSERTION,FREELANCE',
            'duree' => 'nullable|required_if:type_contrat,CDD,INTERIM,FREELANCE,STAGE,APPRENTISSAGE,INSERTION|integer|min:1',
        ]);

        $renouvellement = false;
        $dernierContrat = RhContrat::where('personel_id', $request->collaborateur_id)
            ->orderByDesc('date_debut')
            ->first();
            
        if ($dernierContrat && $dernierContrat->type_contrat === $request->type_contrat) {
            $renouvellement = true;
        }
        $request->merge([
            'personel_id' => $request->collaborateur_id,
            'renouvellement' => $renouvellement,
        ]);


        if (
            $request->has('date_debut') && $request->filled('date_debut')
            && $request->has('duree') && $request->filled('duree')
        ) {

            try {
                $dateDebut = Carbon::parse($request->date_debut);
                $dureeMois = (int) $request->duree;

                // Ajouter les mois
                $dateFin = $dateDebut->copy()->addMonths($dureeMois);

                // Ajuster pour la fin de mois (si le jour n'existe pas dans le mois suivant)
                $jourOriginal = $dateDebut->day;
                $dernierJourMois = $dateFin->copy()->endOfMonth()->day;

                if ($jourOriginal > $dernierJourMois) {
                    $dateFin->day = $dernierJourMois;
                }

                $request->merge([
                    'date_fin' => $dateFin->format('Y-m-d'),
                    'user_id' => auth()->user()->id
                ]);
            } catch (\Exception $e) {
                // Log l'erreur mais continue
                Log::error('Erreur calcul date fin: ' . $e->getMessage());
            }
        }

        // Pour les contrats à durée indéterminée, mettre date_fin à null
        if ($request->type_contrat === RhContrat::CONTRAT_CDI) {
            $request->merge([
                'date_fin' => null,
                'duree' => null
            ]);
        }

        // Mettre à jour via parent::update()
        return parent::update($request, $id);
    }


    public function details($id)
    {
        $contrat = \App\Models\RhContrat::findOrFail($id);

        return view('voyager::rh-contrats.partials.details', compact('contrat'));
    }
}
