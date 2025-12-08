<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PerimetreAction extends Model
{
    protected $table='perimetre_actions';
    protected $fillable = ['libelle_perimetre'];  

    public function wash_actors()
    {
        return $this->hasMany(WashActor::class, 'perimetre_action_id');
    }
}
