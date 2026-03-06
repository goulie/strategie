<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\RhListePersonnel;
use Illuminate\Http\Request;

class ListePersonnelController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
   /*  public function create()
    {
        $steps = [
            'identite' => [
                'title' => 'Identité',
                'fields' => [
                    'matricule',
                    'nom',
                    'prenoms',
                    'sexe',
                    'date_naissance',
                    'lieu_naissance',
                    'nationalite',
                    'cni_passeport'
                ]
            ],
            'situation_personnelle' => [
                'title' => 'Situation Personnelle',
                'fields' => [
                    'situation_matrimoniale',
                    'adresse',
                    'contact_personnel',
                    'email'
                ]
            ],
            'profession' => [
                'title' => 'Informations Professionnelles',
                'fields' => [
                    'service_id',
                    'site_travail',
                    'qualification',
                    'poste',
                    'date_entree'
                ]
            ],
            'administration' => [
                'title' => 'Administration & Statut',
                'fields' => [
                    'num_CNPS',
                    'num_CMU',
                    'status',
                    'commentaire'
                ]
            ],
            'contacts_famille' => [
                'title' => 'Contacts & Famille',
                'fields' => [
                    'nom_complet_personne_urgence',
                    'lien_personne_urgence',
                    'autre_lien_personne_urgence',
                    'contact_personne_urgence',
                    'nom_complet_conjoint',
                    'extrait_conjoint',
                    'extrait_naissance'
                ]
            ]
        ];

        return view('voyager::rh-liste-personnels.create', compact('steps'));
    } */

    public function store(Request $request)
    {
        $data = $request->except('_token');

        RhListePersonnel::create($data);

        return redirect()
            ->route('voyager.personnels.index')
            ->with([
                'message'    => 'Personnel enregistré avec succès',
                'alert-type' => 'success'
            ]);
    }
}
