<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DmsServicesAgr extends Model
{
    protected $table = 'dms_services_agrs';
    protected $fillable = ['libelle_service', 'activite_id', 'statuts', 'tarif_membre', 'tarif_non_membre'];

    
    public function activite()
    {
        return $this->belongsTo(DmsActiviteAgr::class, 'activite_id');
    }



    public static function ActiveAgrServices()
    {
        return self::where('statuts', 'Active')->get();
    }
    
}
