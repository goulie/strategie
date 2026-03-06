<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DmsLigneBudgetaire extends Model
{
    protected $table = 'dms_ligne_budgetaires';  
    protected $fillable = ['libelle_ligne','code_budgetaire_id'];  

    public function code_budgetaire()
    {
        return $this->belongsTo(DmsModuleRecette::class, 'code_budgetaire_id');
    }

    public function dms_membres()
    {
        return $this->hasMany(DmsMembre::class, 'linge_budgetaire_id');
    }
}
