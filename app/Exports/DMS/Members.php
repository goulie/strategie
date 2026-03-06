<?php

namespace App\Exports\DMS;

use App\Models\DmsMembre;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class Members implements FromCollection, WithMapping, WithHeadings
{
    protected $ids;

    public function __construct(array $ids = [])
    {
        $this->ids = $ids;
    }

    /**
     * Récupération des données
     */
    public function collection()
    {
        $query = DmsMembre::with([
            'plan_adhesion',
            'pays',
            'linge_budgetaire'
        ]);

        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Mapping pour chaque ligne
     */
    public function map($membre): array
    {
        return [
            $membre->id,
            $membre->code_membre ?? '',
            $membre->libelle_membre ?? '',
            $membre->email ?? '',
            $membre->organisation ?? '',
            optional($membre->plan_adhesion)->title_plan ?? '',
            optional($membre->pays)->libelle_pays ?? '',
            $membre->date_adhesion
                ? Carbon::parse($membre->date_adhesion)->format('d/m/Y')
                : '',
            optional($membre->linge_budgetaire)->id ?? '',
            $membre->created_at
                ? $membre->created_at->format('d/m/Y H:i')
                : ''
        ];
    }

    /**
     * Définir les en-têtes
     */
    public function headings(): array
    {
        return [
            'ID',
            'Code Membre',
            'Libellé Membre',
            'Email',
            'Organisation',
            'Plan d\'Adhésion',
            'Pays',
            'Date d\'Adhésion',
            'Ligne Budgétaire',
            'Date de création'
        ];
    }
}
