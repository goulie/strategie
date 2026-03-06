<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DmsObjectifsMembre extends Model
{
    protected $table = 'dms_objectifs_membres';
    protected $fillable = ['annee', 'mois', 'objectif','description'];  
}
