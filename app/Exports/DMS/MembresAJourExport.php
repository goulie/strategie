<?php

namespace App\Exports\DMS;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class MembresAJourExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents, ShouldAutoSize
{
    protected $cotisations;
    protected $filters;

    public function __construct($cotisations, $filters = [])
    {
        $this->cotisations = $cotisations;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->cotisations;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Membre',
            'Plan d\'adhésion',
            'Année de cotisation',
            'Montant total attendu (CFA)',
            'Montant payé (CFA)',
            'Reste à payer (CFA)',
            'Pourcentage payé',
            'Statut',
            'Mode de paiement',
            'Référence / N° reçu',
            'Date de paiement',
            'Date d\'échéance',
            'Observation',
            'Enregistré le',
            'Dernière modification'
        ];
    }

    public function map($cotisation): array
    {
        // Calculer le pourcentage
        $pourcentage = $cotisation->montant_total_attendu > 0
            ? round(($cotisation->montant / $cotisation->montant_total_attendu) * 100, 2)
            : 0;

        return [
            $cotisation->id,
            $cotisation->membre->libelle_membre ?? 'N/A',
            $cotisation->membre->plan_adhesion->title_plan ?? 'N/A',
            $cotisation->annee_cotisation,
            $cotisation->montant_total_attendu,
            $cotisation->montant,
            $cotisation->reste_a_payer,
            $pourcentage . ' %',
            $cotisation->status,
            $cotisation->mode_paiement,
            $cotisation->reference ?? 'N/A',
            optional($cotisation->date_paiement)->format('d/m/Y'),
            optional($cotisation->date_echeance)->format('d/m/Y'),
            $cotisation->observation ?? '',
            $cotisation->created_at->format('d/m/Y H:i'),
            $cotisation->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Définir les styles par défaut
        $sheet->getStyle('A1:P1')->getFont()->setBold(true);

        // Style pour les en-têtes
        $sheet->getStyle('A1:P1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4A6785']
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Style pour les montants
        $sheet->getStyle('E2:E' . ($sheet->getHighestRow()))
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('F2:F' . ($sheet->getHighestRow()))
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('G2:G' . ($sheet->getHighestRow()))
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        // Style pour les pourcentages
        $sheet->getStyle('H2:H' . ($sheet->getHighestRow()))
            ->getNumberFormat()
            ->setFormatCode('0.00 %');

        // Alternance de couleurs pour les lignes
        for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
            $color = $i % 2 == 0 ? 'FFFFFF' : 'F8F9FA';
            $sheet->getStyle('A' . $i . ':P' . $i)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color]
                ]
            ]);
        }

        // Bordures
        $sheet->getStyle('A1:P' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ]
            ]
        ]);

        // Alignement
        $sheet->getStyle('A2:A' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('D2:D' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('H2:H' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('I2:I' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('J2:J' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Hauteur des lignes
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 25,  // Membre
            'C' => 20,  // Plan
            'D' => 12,  // Année
            'E' => 20,  // Montant total
            'F' => 18,  // Montant payé
            'G' => 18,  // Reste
            'H' => 15,  // Pourcentage
            'I' => 12,  // Statut
            'J' => 15,  // Mode paiement
            'K' => 20,  // Référence
            'L' => 15,  // Date paiement
            'M' => 15,  // Date échéance
            'N' => 30,  // Observation
            'O' => 20,  // Créé le
            'P' => 20,  // Modifié le
        ];
    }

    public function title(): string
    {
        return 'Cotisations';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Ajouter des informations de filtre
                $sheet = $event->sheet->getDelegate();

                // Insérer les informations de filtre avant le tableau
                $sheet->insertNewRowBefore(1, 5);

                // Titre
                $sheet->setCellValue('A1', 'RAPPORT DES COTISATIONS');
                $sheet->mergeCells('A1:P1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '4A6785']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);

                // Date d'export
                $sheet->setCellValue('A2', 'Date d\'export :');
                $sheet->setCellValue('B2', now()->format('d/m/Y à H:i'));

                // Filtres appliqués
                $sheet->setCellValue('A3', 'Filtres appliqués :');
                $sheet->setCellValue('B3', $this->getFiltersText());

                // Statistiques
                $totalMontant = $this->cotisations->sum('montant');
                $totalReste = $this->cotisations->sum('reste_a_payer');
                $totalAttendu = $this->cotisations->sum('montant_total_attendu');

                $sheet->setCellValue('A4', 'Statistiques :');
                $sheet->setCellValue('B4', 'Montant total payé : ' . number_format($totalMontant, 0, ',', ' ') . ' CFA');
                $sheet->setCellValue('C4', 'Reste total à payer : ' . number_format($totalReste, 0, ',', ' ') . ' CFA');
                $sheet->setCellValue('D4', 'Montant total attendu : ' . number_format($totalAttendu, 0, ',', ' ') . ' CFA');
                $sheet->setCellValue('E4', 'Nombre de cotisations : ' . $this->cotisations->count());

                // Style pour les informations
                $sheet->getStyle('A2:E4')->applyFromArray([
                    'font' => [
                        'size' => 10
                    ]
                ]);

                // Ajouter une ligne de totaux à la fin
                $lastRow = $sheet->getHighestRow() + 1;

                $sheet->setCellValue('A' . $lastRow, 'TOTAUX');
                $sheet->mergeCells('A' . $lastRow . ':D' . $lastRow);

                $sheet->setCellValue('E' . $lastRow, $totalAttendu);
                $sheet->setCellValue('F' . $lastRow, $totalMontant);
                $sheet->setCellValue('G' . $lastRow, $totalReste);

                // Style pour la ligne de totaux
                $sheet->getStyle('A' . $lastRow . ':P' . $lastRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2C3E50']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);

                // Format numérique pour les totaux
                $sheet->getStyle('E' . $lastRow . ':G' . $lastRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                // Ajouter une formule pour le pourcentage moyen
                $avgFormula = '=AVERAGE(H6:H' . ($lastRow - 1) . ')';
                $sheet->setCellValue('H' . $lastRow, $avgFormula);
                $sheet->getStyle('H' . $lastRow)
                    ->getNumberFormat()
                    ->setFormatCode('0.00 %');

                // Ajuster la hauteur de la ligne de totaux
                $sheet->getRowDimension($lastRow)->setRowHeight(25);

                // Déplacer le tableau vers le bas
                /* $sheet->fromArray($this->headings(), null, 'A5'); */

                // Remplir les données
                $row = 7;
                foreach ($this->cotisations as $cotisation) {
                    $sheet->fromArray($this->map($cotisation), null, 'A' . $row);
                    $row++;
                }
            }
        ];
    }

    private function getFiltersText()
    {
        $filters = [];

        if (!empty($this->filters['year']) && $this->filters['year'] !== 'all') {
            $filters[] = 'Année : ' . $this->filters['year'];
        }

        if (!empty($this->filters['search'])) {
            $filters[] = 'Recherche : "' . $this->filters['search'] . '"';
        }

        return empty($filters) ? 'Aucun filtre' : implode(' | ', $filters);
    }
}
