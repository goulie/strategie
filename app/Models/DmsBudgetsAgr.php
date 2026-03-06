<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class DmsBudgetsAgr extends Model
{
    protected $table = 'dms_budgets_agrs';
    protected $fillable = ['activity_id', 'year', 'amount'];

    public function activite()
    {
        return $this->belongsTo(DmsActiviteAgr::class, 'activity_id');
    }

    public static function getTauxRealisationAnnuel($year)
    {
        $budget_total = self::where('year', $year)->sum('amount');

        $ca_reel = DmsAgrCotisation::CA_annuelle($year);

        // Calcul du taux de réalisation
        if ($budget_total > 0) {
            $taux_realisation = ($ca_reel['ca'] / $budget_total) * 100;
        } else {
            $taux_realisation = $ca_reel['ca'] > 0 ? 100 : 0;
        }

        return [
            'year' => $year,
            'budget_total' => $budget_total,
            'ca_reel' => $ca_reel,
            'taux_realisation' => round($taux_realisation, 2),
            'statut' => $taux_realisation >= 100 ? 'atteint' : 'en_cours'
        ];
    }

    public static function yearsList()
    {
        $years = range(2022, now()->year + 1);
        return array_combine($years, $years);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {

            if ($model->activity_id == null) {
                throw ValidationException::withMessages([
                    'activity_id' => 'Veuillez choisir une activité.'
                ]);
            }
            $exists = self::where('activity_id', $model->activity_id)
                ->where('year', $model->year)
                ->when($model->id, function ($query) use ($model) {
                    return $query->where('id', '!=', $model->id);
                })
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'activity_id' => 'Cette activité existe déjà pour cette année.'
                ]);
            }

            $maxYear = now()->year + 1;

            if ($model->year < 2022 || $model->year > $maxYear) {
                throw ValidationException::withMessages([
                    'year' => "L'année doit être entre 2022 et {$maxYear}."
                ]);
            }
        });
    }
}
