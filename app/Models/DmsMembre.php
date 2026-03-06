<?php

namespace App\Models;

use App\Exports\DMS\Members;
use App\Traits\RH\HasActivityLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DmsMembre extends Model
{
    use HasActivityLog;
    
    protected $table = 'dms_membres';
    protected $fillable = [
        'plan_adhesion_id',
        'pays_id',
        'libelle_membre',
        'email',
        'organisation',
        'date_adhesion',
        'linge_budgetaire_id',
        'code_membre'
    ];

    public $allow_export_all = FALSE;
    public $export_handler = Members::class;

    public function plan_adhesion()
    {
        return $this->belongsTo(DmsPlanAdhesion::class, 'plan_adhesion_id');
    }

    public function pays()
    {
        return $this->belongsTo(Pay::class, 'pays_id');
    }

    public function linge_budgetaire()
    {
        return $this->belongsTo(DmsLigneBudgetaire::class, 'linge_budgetaire_id');
    }

    public static function nbMembresParMois($annee = null)
    {
        $annee = $annee ?? now()->year;
        return DB::table('dms_membres')->select(
            DB::raw('MONTH(date_adhesion) as mois'),
            DB::raw('COUNT(id) as total_membres')
        )
            ->whereYear('date_adhesion', $annee)
            ->groupBy(DB::raw('MONTH(date_adhesion)'))
            ->orderBy('mois')
            ->get();
    }

    public static function getMembresParMois($annee)
    {
        return self::whereYear('date_adhesion', $annee)
            ->select(
                DB::raw('MONTH(date_adhesion) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('MONTH(date_adhesion)'))
            ->orderBy('mois')
            ->get()
            ->keyBy('mois');
    }
    public static function getMembresAvecFiltres($annee, $search = null)
    {
        $query = self::whereYear('date_adhesion', $annee);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('libelle_membre', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('code_membre', 'LIKE', "%{$search}%")
                    ->orWhere('organisation', 'LIKE', "%{$search}%");
            });
        }

        return $query->orderBy('date_adhesion', 'desc')->get();
    }

    // Dans DmsMembre.php
    public function cotisations()
    {
        return $this->hasMany(DmsCotisationMembre::class, 'membre_id');
    }

    public function scopeAJourPourAnnee(Builder $query, $annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return $query->whereHas('cotisations', function ($q) use ($annee) {
            $q->where('annee_cotisation', $annee)
                ->where('date_echeance', '>=', Carbon::now()->format('Y-m-d'));
        });
    }

    public function scopeNonAJourPourAnnee(Builder $query, $annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return $query->whereDoesntHave('cotisations', function ($q) use ($annee) {
            $q->where('annee_cotisation', $annee)
                ->where('date_echeance', '>=', Carbon::now()->format('Y-m-d'));
        });
    }

    public function estAJourPourAnnee($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return $this->cotisations()
            ->where('annee_cotisation', $annee)
            ->where('date_echeance', '>=', Carbon::now()->format('Y-m-d'))
            ->exists();
    }

    public function getCotisationPourAnnee($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return $this->cotisations()
            ->where('annee_cotisation', $annee)
            ->where('date_echeance', '>=', Carbon::now()->format('Y-m-d'))
            ->first();
    }

    
}
