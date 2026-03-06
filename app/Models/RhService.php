<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RhService extends Model
{
    protected $table = 'rh_services';
    protected $fillable = ['libelle_service', 'departement_id'];

    protected $appends = ['personnels_count'];

    public function departement()
    {
        return $this->belongsTo(RhDirection::class, 'departement_id');
    }

    public function rhListes()
    {
        return $this->hasMany(RhListePersonnel::class, 'Service_id');
    }

    public function getPersonnelsCountAttribute()
    {
        return $this->rh_listes_count ?? $this->rhListes()->count();
    }

    public function scopeWithPersonnelCount($query)
    {
        return $query->withCount('rhListes');
    }
}
