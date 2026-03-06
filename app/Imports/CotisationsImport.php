<?php

namespace App\Imports;

use App\Models\DmsCotisationMembre;
use App\Models\DmsMembre;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithValidation;

class CotisationsImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $errors = [];
    private $imported = 0;
    private $failed = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Vérifier si les en-têtes sont présents
                if (!isset($row['matricule']) || !isset($row['annee'])) {
                    $this->errors[] = "Ligne ignorée : en-têtes manquants";
                    $this->failed++;
                    continue;
                }

                // Vérifier si le membre existe
                $membre = DmsMembre::where('code_membre', $row['matricule'])->first();

                if (!$membre) {
                    $this->errors[] = "Matricule {$row['matricule']} : membre non trouvé";
                    $this->failed++;
                    continue;
                }

                // Vérifier si une cotisation existe déjà pour cette année
                $cotisationExistante = DmsCotisationMembre::where('membre_id', $membre->id)
                    ->where('annee_cotisation', $row['annee'])
                    ->first();

                if ($cotisationExistante) {
                    $this->errors[] = "Matricule {$row['matricule']} : cotisation existe déjà pour l'année {$row['annee']}";
                    $this->failed++;
                    continue;
                }

                // LOGIQUE SIMPLIFIÉE POUR LA DATE DE PAIEMENT
                $datePaiement = null;

                if (isset($row['date_paiement']) && !empty(trim($row['date_paiement']))) {
                    $dateValue = trim($row['date_paiement']);

                    // Si la valeur n'est pas "N/A", essayer de parser la date
                    if (strtoupper($dateValue) !== 'N/A') {
                        // Essayer le format d/m/Y (01/01/2022)
                        try {
                            $datePaiement = Carbon::createFromFormat('d/m/Y', $dateValue);
                        } catch (\Exception $e) {
                            // Si échec, essayer d'autres formats courants
                            try {
                                $datePaiement = Carbon::createFromFormat('Y-m-d', $dateValue);
                            } catch (\Exception $e) {
                                try {
                                    $datePaiement = Carbon::parse($dateValue);
                                } catch (\Exception $e) {
                                    // Si tout échoue, utiliser 1/1/année
                                    $datePaiement = Carbon::create($row['annee'], 1, 1);
                                }
                            }
                        }
                    } else {
                        // Si "N/A", utiliser 1/1/année
                        $datePaiement = Carbon::create($row['annee'], 1, 1);
                    }
                } else {
                    // Si vide ou non défini, utiliser 1/1/année
                    $datePaiement = Carbon::create($row['annee'], 1, 1);
                }

                // Formater en Y-m-d
                $datePaiement = $datePaiement->format('Y-m-d');

                // Calculer la date d'échéance (31 mars de l'année suivante)
                $dateEcheance = Carbon::create($row['annee'] + 1, 3, 31)->format('Y-m-d');

                // Déterminer le statut
                $status = 'TOTAL';

                // Vérifier que $datePaiement est bien au format Y-m-d
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $datePaiement)) {
                    // Forcer à 1/1/année si le format est invalide
                    $datePaiement = Carbon::create($row['annee'], 1, 1)->format('Y-m-d');
                }

                // Créer la cotisation
                $cotisation = DmsCotisationMembre::create([
                    'membre_id' => $membre->id,
                    'annee_cotisation' => $row['annee'],
                    'date_paiement' => $datePaiement,
                    'date_echeance' => $dateEcheance,
                    'status' => $status,
                    'montant' => $membre->plan_adhesion->price_xof,
                    'reste_a_payer' => 0,
                    'mode_paiement' => 'virement',
                    'observation' => 'Importé via Excel',
                    'reference' => 'N/A',
                ]);

                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = "Matricule {$row['matricule']} : " . $e->getMessage();
                $this->failed++;
            }
        }
    }

    public function rules(): array
    {
        return [
            'matricule' => 'required|string',
            'annee' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'matricule.required' => 'Le matricule est requis',
            'annee.required' => 'L\'année est requise',
            'annee.integer' => 'L\'année doit être un nombre entier',
            'annee.min' => 'L\'année doit être supérieure ou égale à 2000',
            'annee.max' => 'L\'année ne peut pas être dans le futur lointain',
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getFailedCount(): int
    {
        return $this->failed;
    }

    public function getResults(): array
    {
        return [
            'imported' => $this->imported,
            'failed' => $this->failed,
            'errors' => $this->errors,
        ];
    }
}
