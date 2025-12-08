<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Region extends Model
{ 
    protected $table = 'regions';
    protected $fillable = ['libelle'];

    public function wash_actors()
    {
        return $this->hasMany(WashActor::class, 'region_id');
    }

    public function gouvernances()
    {
        return $this->hasMany(Gouvernance::class, 'region_id');
    }
}
