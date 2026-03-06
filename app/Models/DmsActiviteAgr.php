<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DmsActiviteAgr extends Model
{
    protected $table = 'dms_activite_agrs';
    protected $fillable = ['libelle_activite', 'statuts'];

    public function scopeActive($query)
    {
        return $query->where('statuts', 'Active');
    }

    public static function ActiveAgrActivities()
    {
        return self::where('statuts', 'Active')->get();
    }
}
