<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Gouvernance extends Model
{
    protected $table = 'gouvernances';
    protected $fillable = [
        'status',
        'annee',
        'continent_id',
        'region_id',
        'pays_id',
        'sigle_societe',
        'date_creation',
        'denomination',
        'ep_eu',
        'membre',
        'representant',
        'genre_id',
        'fonction',
        'date_denomination',
        'num_ordre',
        'observation',
    ];

    public function pays()
    {
        return $this->belongsTo(Pay::class, 'pays_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function continent()
    {
        return $this->belongsTo(Continent::class, 'continent_id');
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }
}
