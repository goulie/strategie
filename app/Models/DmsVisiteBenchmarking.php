<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DmsVisiteBenchmarking extends Model
{
    protected $table = 'dms_visite_benchmarkings';
    protected $fillable = [
        'libelle_visite',
        'date_debut',
        'date_fin',
        'duree',
        'prix_membre',
        'prix_non_membre'
    ];

    public static function ActivesListBenchmark()
    {
        return self::whereYear('date_debut', date('Y'))->get();
    }

    public static function MemberAmountBenchmark($id)
    {
        return self::find($id)->prix_membre;
    }

    public static function NonMemberAmountBenchmark($id)
    {
        return self::find($id)->prix_non_membre;
    }
}
