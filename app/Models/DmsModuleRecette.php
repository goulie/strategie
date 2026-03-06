<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DmsModuleRecette extends Model
{
    protected $table = 'dms_module_recettes';  
    protected $fillable = ['libelle_module','ligne_budgetaire'];
}
