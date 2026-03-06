<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DmsAgrFacturation extends Model
{
    protected $table = 'dms_agr_facturations';
    protected $fillable = [
        'membre',
        'activiy_id',
        'date_facturation',
        'annee_facturation',
        'montant',
        'devise',
        'status',
        'observations'
    ];

    public function LigneBudgetaire()
    {
        return $this->belongsTo(DmsLigneBudgetaire::class, 'activiy_id');
    }

    public static function Activities()
    {
        $activities = DmsLigneBudgetaire::join('dms_module_recettes', 'dms_ligne_budgetaires.code_budgetaire_id', '=', 'dms_module_recettes.id')
            ->Orwhere('dms_module_recettes.ligne_budgetaire', 'C1040')
            ->Orwhere('dms_module_recettes.ligne_budgetaire', 'C1050')
            ->Orwhere('dms_module_recettes.ligne_budgetaire', 'C1060')
            ->Orwhere('dms_module_recettes.ligne_budgetaire', 'C1070')
            ->Orwhere('dms_module_recettes.ligne_budgetaire', 'C1080')
            ->Orwhere('dms_module_recettes.ligne_budgetaire', 'C1090')
            ->select('dms_ligne_budgetaires.libelle_ligne as libelle', 'dms_ligne_budgetaires.id as id')
            ->get();

        return $activities;
    }
}
