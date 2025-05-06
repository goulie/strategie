<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RegleRetraitTarif extends Model
{
    protected $table = 'regle_retrait_tarifs';
    protected $fillable = [
        'type',
        'valeur',
        'unite'
    ];
}
