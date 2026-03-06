<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DmsEvenementsAgr extends Model
{
    protected $table = 'dms_evenements_agrs';

    protected $fillable = [
        'libelle_event',
        'user_id',
        'date_event',
        'duree',
        'status',
    ];

    public function depenses()
    {
        return $this->hasMany(DmsAgrDepense::class, 'event_id');
    }

    public function Cotisations()
    {
        return $this->hasMany(DmsAgrCotisation::class, 'evenement_id');
    }

    public static function getSumDepenses($eventId)
    {
        self::where('event_id', $eventId)
                ->sum('cout_depense');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
    public static function ActiveEventsAgrs()
    {
        return self::where('status', 'Active')->get();
    }


    
    public function save(array $options = [])
    {
        if (!$this->user_id && Auth::user()) {
            $this->user_id = Auth::user()->id;
        }

        return parent::save();
    }
}
