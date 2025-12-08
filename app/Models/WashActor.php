<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class WashActor extends Model
{
    protected $table = 'wash_actors';
    protected $fillable = [
        'annees_creation',
        'date',
        'pays_id',
        'region_id',
        'perimetre_action_id',
        'etats',
        'sigle',
        'ministere',
        'denomination',
        'decret_creation',
        'role',
        'activite',
        'public_or_prive',
        'regulateur_patrimoine',
        'secteur',
        'energie',
        'adresse_siege',
        'contact',
    ];

    public function pays()
    {
        return $this->belongsTo(Pay::class, 'pays_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function perimetreAction()
    {
        return $this->belongsTo(PerimetreAction::class, 'perimetre_action_id');
    }
}
