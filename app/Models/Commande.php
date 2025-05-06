<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Commande extends Model
{
    protected $table = 'commandes';
    protected $fillable = [
        'date_depot',
        'date_retrait_prevue',
        'date_retrait_reelle',
        'statut',
        'montant_total',
        'accompte',
        'reduction',
        'commentaires',
        'mode_paiement',
        'code',
        'type_retrait',
        'etat_retrait'
    ];
}
