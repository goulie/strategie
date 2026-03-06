<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RhDirection extends Model
{
    protected $table = 'rh_directions';
    protected $fillable = ['libelle_directions'];

    protected $appends = ['personnels_count'];

    public function services()
    {
        return $this->hasMany(RhService::class, 'departement_id');
    }

    public function getPersonnelsCountAttribute()
    {
        // Si déjà chargé avec eager loading
        if ($this->relationLoaded('services')) {
            return $this->services->sum(function ($service) {
                return $service->rh_listes_count
                    ?? $service->rhListes()->count();
            });
        }

        // Sinon requête optimisée
        return RhListePersonnel::whereIn(
            'Service_id',
            $this->services()->pluck('id')
        )->count();
    }

    public function scopeWithPersonnelCount($query)
    {
        return $query->with([
            'services' => function ($q) {
                $q->withCount('rhListes');
            }
        ]);
    }
    
}
