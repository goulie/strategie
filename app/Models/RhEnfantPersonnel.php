<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class RhEnfantPersonnel extends Model
{
    protected $table = "rh_enfant_personnels";
    protected $primaryKey = "id";
    protected $fillable = [
        'nom_complet',
        'date_naissance',
        'extrait_naissance',
        'personnel_id',
        'sexe',
        'status','image'
    ];

    /**
     * Accessor pour l'âge
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->date_naissance) {
                    return null;
                }

                return Carbon::parse($this->date_naissance)->age;
            }
        );
    }

    /**
     * Accessor pour l'âge avec format détaillé
     */
    protected function ageDetail(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->date_naissance) {
                    return 'Date non spécifiée';
                }

                $naissance = Carbon::parse($this->date_naissance);
                $now = Carbon::now();

                $ans = $naissance->diffInYears($now);
                $mois = $naissance->copy()->addYears($ans)->diffInMonths($now);

                if ($ans == 0) {
                    return "{$mois} mois";
                } elseif ($mois == 0) {
                    return "{$ans} ans";
                } else {
                    return "{$ans} ans et {$mois} mois";
                }
            }
        );
    }

    public function personnel()
    {
        return $this->belongsTo(RhListePersonnel::class, 'personnel_id');
    }

    /**
     * Scope pour filtrer par âge minimum
     */
    public function scopeAgeMin($query, $age)
    {
        if ($age) {
            $dateLimite = Carbon::now()->subYears($age);
            return $query->where('date_naissance', '<=', $dateLimite);
        }
        return $query;
    }

    /**
     * Scope pour filtrer par âge maximum
     */
    public function scopeAgeMax($query, $age)
    {
        if ($age) {
            $dateLimite = Carbon::now()->subYears($age + 1);
            return $query->where('date_naissance', '>', $dateLimite);
        }
        return $query;
    }
}
