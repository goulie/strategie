<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DmsAgrDepense extends Model
{
    protected $table = 'dms_agr_depenses';
    protected $fillable = [
        'event_id',
        'libelle_depense',
        'cout_depense',
        'register_user_id',
        'observations'
    ];


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {

            // Vérifier utilisateur connecté
            if (!Auth::check()) {
                throw ValidationException::withMessages([
                    'register_user_id' => 'Utilisateur non authentifié.'
                ]);
            }

            // Assigner automatiquement l'utilisateur connecté
            $model->register_user_id = Auth::id();

            // Validation des champs obligatoires
            if (empty($model->event_id)) {
                throw ValidationException::withMessages([
                    'event_id' => 'Veuillez choisir un évènement.'
                ]);
            }

            if (empty($model->libelle_depense)) {
                throw ValidationException::withMessages([
                    'libelle_depense' => 'Le libellé de la dépense est obligatoire.'
                ]);
            }

            if (empty($model->cout_depense)) {
                throw ValidationException::withMessages([
                    'cout_depense' => 'Le coût de la dépense est obligatoire.'
                ]);
            }

            if (empty($model->observations)) {
                throw ValidationException::withMessages([
                    'observations' => 'Les observations sont obligatoires.'
                ]);
            }
        });
    }

    public function scopeActiveEvents($query)
    {
        return $query->whereHas('event', function ($q) {
            $q->where('statuts', 'Active');
        });
    }


    public function event()
    {
        return $this->belongsTo(DmsEvenementsAgr::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'register_user_id');
    }

    public static function getSumDepensesByEvent($eventId)
    {
        return self::where('event_id', $eventId)
            ->sum('cout_depense');
    }
}
