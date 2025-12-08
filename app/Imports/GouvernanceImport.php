<?php

namespace App\Imports;

use App\Models\Continent;
use App\Models\Region;
use App\Models\Pay;
use App\Models\Genre;
use App\Models\Gouvernance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GouvernanceImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 🔹 1. Trouver ou créer Continent
        $continent = Continent::firstOrCreate([
            'libelle' => $row['continent']
        ]);

        // 🔹 2. Trouver ou créer Région
        $region = Region::firstOrCreate([
            'libelle' => $row['region']
        ]);

        // 🔹 3. Trouver ou créer Pays
        $pays = Pay::firstOrCreate([
            'libelle_pays' => $row['pays']
        ]);

        // 🔹 4. Trouver ou créer Genre
        $genre = Genre::firstOrCreate([
            'libelle_genre' => $row['genre']
        ]);

        // 🔹 5. Création de Gouvernance
        return new Gouvernance([
            'status'            => $row['status'],
            'annee'             => $row['annee'],
            'continent_id'      => $continent->id,
            'region_id'         => $region->id,
            'pays_id'           => $pays->id,
            'sigle_societe'     => $row['sigle_societe'],
            'date_creation'     => $row['date_creation'],
            'denomination'      => $row['denomination'],
            'ep_eu'             => $row['ep_eu'],
            'membre'            => $row['membre'],
            'representant'      => $row['representant'],
            'genre_id'          => $genre->id,
            'fonction'          => $row['fonction'],
            'date_denomination' => $row['date_denomination'],
            'num_ordre'         => $row['num_ordre'],
            'observation'       => $row['observation'],
        ]);
    }
}
