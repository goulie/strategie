<?php

namespace App\Imports;

use App\Models\Continent;
use App\Models\Region;
use App\Models\Pay;
use App\Models\Genre;
use App\Models\Gouvernance;
use App\Models\RhListePersonnel;
use App\Models\RhService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GouvernanceImport implements ToModel, WithHeadingRow
{

    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // 🔹 Cas 1 : Date Excel (numérique)
        if (is_numeric($value)) {
            return Carbon::instance(
                ExcelDate::excelToDateTimeObject($value)
            )->format('Y-m-d');
        }

        // 🔹 Cas 2 : Date texte (01/08/2023)
        try {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null; // ou throw exception si tu veux bloquer
        }
    }
    public function model(array $row)
    {


        // 🔹 Service (find or create)
        $service = RhService::firstOrCreate([
            'libelle_service' => $row['service']
        ]);

        return new RhListePersonnel([
            'Matricule'               => $row['matricule'],
            'Nom'                     => $row['nom'],
            'Prenoms'                 => $row['prenom'],
            'Sexe'                    => $row['sexe'],
            'Date_naissance'          => $this->parseDate($row['date_naissance']),
            'Lieu_naissance'          => $row['lieu_naissance'],
            'Nationalite'             => $row['nationalite'],
            'cni_passeport'           => $row['cni_passeport'],
            'Situation_matrimoniale'  => $row['situation_matrimoniale'],
            'Nombre_enfant'           => $row['nombre_enfant'],
            'contact_personnel'       => $row['contact_personnel'],
            'Email'                   => $row['email'],
            'Personne_urgence'        => $row['personne_urgence'],
            'Contact_urgence'        => $row['contact_urgence'],
            'Service_id'              => $service->id,
            'site_travail'            => $row['site_travail'],
            'Type_contrat'            => $row['type_contrat'],
            'Date_entree'             => $this->parseDate($row['date_entree']),
            'Date_fin_contrat'        => $this->parseDate($row['date_fin_contrat']),
            'Num_CNPS'                => $row['num_cnps'],
            'Num_CMU'                 => $row['num_cmu'],
            'Status'                  => $row['status'],
            'Commentaire'             => $row['commentaire'],
            'Adresse'                 => $row['adresse'],
            'poste'                   => $row['poste'],
        ]);
    }
}
