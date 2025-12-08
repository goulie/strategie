<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Pay extends Model
{
  protected $table='pays';
  protected $fillable = ['libelle_pays'];  

  public function gouvernances()
  {
      return $this->hasMany(Gouvernance::class, 'pays_id');
  }

  public function wash_actors()
  {
      return $this->hasMany(WashActor::class, 'pays_id');
  }
}
