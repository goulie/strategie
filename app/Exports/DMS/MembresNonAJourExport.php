<?php

namespace App\Exports\DMS;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class MembresNonAJourExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents, ShouldAutoSize
{
    protected $membres;
    protected $annee;
    protected $dateReference;

    public function __construct($membres, $annee = null, $dateReference = null)
    {
        $this->membres = $membres;
        $this->annee = $annee ?? date('Y');
        $this->dateReference = $dateReference ?? now();
    }

    public function collection()
    {
        return $this->membres;
    }

    public function headings(): array
    {
        return [
            'ID Membre',
            'Nom du membre',
            'Plan d\'adhésion',
            'Montant annuel (CFA)',
            'Année vérifiée',
            'Dernière cotisation',
            'Date dernier paiement',
            'Montant dernier paiement (CFA)',
            'Montant total payé (CFA)',
            'Reste à payer (CFA)',
            'Statut',
            /* 'Jours sans cotisation',
            'En retard depuis',
            'Contact',
            'Email',
            'Date d\'adhésion',
            'Dernière mise à jour' */
        ];
    }

    public function map($membre): array
    {
        // Récupérer la dernière cotisation pour l'année spécifiée
        $derniereCotisation = $membre->cotisations()
            ->where('annee_cotisation', $this->annee)
            ->orderBy('date_paiement', 'desc')
            ->first();

        // Calculer le total payé pour l'année
        $totalPaye = $membre->cotisations()
            ->where('annee_cotisation', $this->annee)
            ->sum('montant');

        // Montant attendu selon le plan
        $montantAttendu = $membre->plan_adhesion->price_xof ?? 0;
        $resteAPayer = max(0, $montantAttendu - $totalPaye);

        // Calculer les jours sans cotisation
        $joursSansCotisation = null;
        $enRetardDepuis = null;

        if ($derniereCotisation && $derniereCotisation->date_echeance) {
            $dateEcheance = Carbon::parse($derniereCotisation->date_echeance);
            $joursSansCotisation = $dateEcheance->diffInDays($this->dateReference, false);

            if ($joursSansCotisation > 0) {
                $enRetardDepuis = $dateEcheance->format('d/m/Y');
            }
        }

        // Déterminer le statut
        if ($totalPaye >= $montantAttendu) {
            $statut = 'À jour';
            $statutColor = 'success';
        } elseif ($totalPaye > 0) {
            $statut = 'Partiel';
            $statutColor = 'warning';
        } else {
            $statut = 'Non cotisé';
            $statutColor = 'danger';
        }

        // Ajouter l'indicateur de retard si applicable
        if ($joursSansCotisation > 30) {
            $statut = 'En retard (' . $joursSansCotisation . ' jours)';
        }

        return [
            $membre->id,
            $membre->libelle_membre,
            $membre->plan_adhesion->title_plan ?? 'Non spécifié',
            $montantAttendu,
            $this->annee,
            $derniereCotisation ? 'Oui' : 'Non',
            $derniereCotisation ? $derniereCotisation->date_paiement : 'Jamais',
            $derniereCotisation ? $derniereCotisation->montant : 0,
            $totalPaye,
            $resteAPayer,
            $statut,
/*             $joursSansCotisation ? abs($joursSansCotisation) : 0,
            $enRetardDepuis ?? 'À jour',
            $membre->telephone ?? 'Non renseigné',
            $membre->email ?? 'Non renseigné',
            $membre->created_at->format('d/m/Y'),
            $membre->updated_at->format('d/m/Y H:i') */
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styles de base
        $sheet->getStyle('A1:Q1')->getFont()->setBold(true);

        // Style des en-têtes
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC3545'] // Rouge pour "non à jour"
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]

        ]);

        // Style pour les montants
        $sheet->getStyle('D2:D' . ($sheet->getHighestRow()))
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('H2:H' . ($sheet->getHighestRow()))
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('I2:I' . ($sheet->getHighestRow()))
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('J2:J' . ($sheet->getHighestRow()))
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        // Style conditionnel pour les statuts
        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $statutCell = $sheet->getCell('K' . $row);
            $statut = $statutCell->getValue();

            if (str_contains($statut, 'À jour')) {
                $sheet->getStyle('K' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D4EDDA']
                    ],
                    'font' => [
                        'color' => ['rgb' => '155724']
                    ]
                ]);
            } elseif (str_contains($statut, 'Partiel')) {
                $sheet->getStyle('K' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF3CD']
                    ],
                    'font' => [
                        'color' => ['rgb' => '856404']
                    ]
                ]);
            } else {
                $sheet->getStyle('K' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8D7DA']
                    ],
                    'font' => [
                        'color' => ['rgb' => '721C24']
                    ]
                ]);
            }

            // Style pour les jours de retard
            $joursCell = $sheet->getCell('L' . $row);
            $jours = $joursCell->getValue();

            if ($jours > 90) {
                $sheet->getStyle('L' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '721C24']
                    ]
                ]);
            } elseif ($jours > 30) {
                $sheet->getStyle('L' . $row)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '856404']
                    ]
                ]);
            }
        }

        // Alternance de couleurs des lignes
        for ($i = 2; $i <= $highestRow; $i++) {
            $color = $i % 2 == 0 ? 'FFFFFF' : 'F8F9FA';
            $sheet->getStyle('A' . $i . ':Q' . $i)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color]
                ]
            ]);
        }

        // Bordures
        $sheet->getStyle('A1:Q' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ]
            ]
        ]);

        // Alignement
        $alignCenter = ['A', 'E', 'F', 'K', 'L'];
        foreach ($alignCenter as $col) {
            $sheet->getStyle($col . '2:' . $col . $highestRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->getStyle('D2:J' . $highestRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Hauteur des lignes
        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }

    public function title(): string
    {
        return 'Membres Non à Jour ' . $this->annee;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insérer un en-tête informatif
                $sheet->insertNewRowBefore(1, 6);

                // Titre principal
                $sheet->setCellValue('A1', 'RAPPORT DES MEMBRES NON À JOUR');
                $sheet->mergeCells('A1:Q1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                        'color' => ['rgb' => 'DC3545']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);

                // Sous-titre
                $sheet->setCellValue('A2', 'Liste des membres ayant des cotisations en retard ou incomplètes');
                $sheet->mergeCells('A2:Q2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 12,
                        'color' => ['rgb' => '6C757D']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);

                // Informations de rapport
                $sheet->setCellValue('A3', 'Année de référence:');
                $sheet->setCellValue('B3', $this->annee);

                $sheet->setCellValue('A4', 'Date du rapport:');
                $sheet->setCellValue('B4', now()->format('d/m/Y à H:i'));

                $totalMembres = $this->membres->count();
                $sheet->setCellValue('A5', 'Nombre de membres non à jour:');
                $sheet->setCellValue('B5', $totalMembres);

                // Calcul des statistiques
                $membresNonCotises = $this->membres->filter(function ($membre) {
                    $totalPaye = $membre->cotisations()
                        ->where('annee_cotisation', $this->annee)
                        ->sum('montant');
                    return $totalPaye == 0;
                })->count();

                $membresPartiels = $this->membres->filter(function ($membre) {
                    $totalPaye = $membre->cotisations()
                        ->where('annee_cotisation', $this->annee)
                        ->sum('montant');
                    $montantAttendu = $membre->plan_adhesion->price_xof ?? 0;
                    return $totalPaye > 0 && $totalPaye < $montantAttendu;
                })->count();

                $sheet->setCellValue('E3', 'Statistiques:');
                $sheet->setCellValue('E4', 'Membres non cotisés: ' . $membresNonCotises);
                $sheet->setCellValue('E5', 'Membres partiels: ' . $membresPartiels);
                $sheet->setCellValue('E6', 'Membres en retard: ' . ($totalMembres - $membresNonCotises - $membresPartiels));

                // Style des informations
                $sheet->getStyle('A3:B6')->applyFromArray([
                    'font' => [
                        'size' => 10
                    ]
                ]);

                $sheet->getStyle('E3:E6')->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'bold' => true
                    ]
                ]);

                // Déplacer les en-têtes du tableau
                $sheet->fromArray($this->headings(), null, 'A7');

                // Remplir les données
                $row = 8;
                foreach ($this->membres as $membre) {
                    $sheet->fromArray($this->map($membre), null, 'A' . $row);
                    $row++;
                }

                // Ajouter des totaux
                $lastRow = $sheet->getHighestRow() + 2;

                $sheet->setCellValue('A' . $lastRow, 'RÉCAPITULATIF');
                $sheet->mergeCells('A' . $lastRow . ':C' . $lastRow);

                $sheet->setCellValue('D' . $lastRow, '=SUM(D8:D' . ($row - 1) . ')');
                $sheet->setCellValue('I' . $lastRow, '=SUM(I8:I' . ($row - 1) . ')');
                $sheet->setCellValue('J' . $lastRow, '=SUM(J8:J' . ($row - 1) . ')');

                // Style du récapitulatif
                $sheet->getStyle('A' . $lastRow . ':K' . $lastRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '343A40']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);

                // Format des totaux
                $sheet->getStyle('D' . $lastRow . ':J' . $lastRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                // Ajuster les largeurs de colonnes
                foreach (range('A', 'Q') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Protéger la feuille (optionnel)
                // $sheet->getProtection()->setSheet(true);

                // Ajouter un filtre automatique
                $sheet->setAutoFilter('A7:Q7');
            }
        ];
    }
}
