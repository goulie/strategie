<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;


class DmsObjectifsMembre extends Model
{
    protected $table = 'dms_objectifs_membres';
    protected $fillable = ['annee', 'mois', 'objectif', 'description'];


    protected static function booted()
    {
        static::creating(function ($model) {

            $exists = self::where('annee', $model->annee)
                ->where('mois', $model->mois)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'mois' => 'Ce mois existe déjà pour l’année sélectionnée.'
                ]);
            }

            if ((int) $model->mois === 13) {

                for ($m = 1; $m <= 12; $m++) {

                    self::create([
                        'annee'     => $model->annee,
                        'mois'      => $m,
                        'objectif'  => $model->objectif,
                        'description'  => $model->description,
                    ]);
                }

                return false;
            }
        });
    }
    public static function getObjectifsParAnnee($annee)
    {
        return self::where('annee', $annee)
            ->orderBy('mois')
            ->get()
            ->keyBy('mois');
    }

    // Obtenir ou créer un objectif par défaut
    public static function getOuCreerObjectif($annee, $mois, $valeurParDefaut = 50)
    {
        return self::firstOrCreate(
            ['annee' => $annee, 'mois' => $mois],
            ['objectif' => $valeurParDefaut]
        );
    }
}
