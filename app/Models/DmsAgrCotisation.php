<?php

namespace App\Models;

use App\Traits\RH\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DmsAgrCotisation extends Model
{
    use HasActivityLog;
    protected $table = 'dms_agr_cotisations';
    protected $fillable = [
        'date_paiement',
        'annee',
        'observations',
        'type_contributeur',
        'membre_id',
        'nom_complet',
        'organisation',
        'email',
        'telephone',
        'pays_id',
        'service_id',
        'activite_id',
        'evenement_id',
        'quantite',
        'prix_unitaire',
        'montant_total',
        'montant_normal',
        'type_tarif',
        'member_is_up_to_date',
    ];


    public static function getSumCotisationsByEvent($eventId)
    {
        return self::where('evenement_id', $eventId)
            ->sum('montant_total');
    }


    public function service()
    {
        return $this->belongsTo(DmsServicesAgr::class, 'service_id');
    }

    public function pays()
    {
        return $this->belongsTo(Pay::class, 'pays_id');
    }

    public function activite()
    {
        return $this->belongsTo(DmsActiviteAgr::class, 'activite_id');
    }

    public function evenement()
    {
        return $this->belongsTo(DmsEvenementsAgr::class, 'evenement_id');
    }
    public function membre()
    {
        return $this->belongsTo(DmsMembre::class, 'membre_id');
    }

    public static function Activities()
    {
        $activities = DmsActiviteAgr::where('statuts', 'Active')->get();

        return $activities;
    }

    public static function CA_annuelle($year)
    {
        $ca_current = self::where('annee', $year)->sum('montant_total');
        $ca_previous = self::where('annee', $year - 1)->sum('montant_total');

        // Éviter division par zéro
        if ($ca_previous > 0) {
            $percentage = (($ca_current - $ca_previous) / $ca_previous) * 100;
        } else {
            $percentage = $ca_current > 0 ? 100 : 0;
        }

        // Déterminer la tendance
        if ($percentage > 0) {
            $trend = 'up';
        } elseif ($percentage < 0) {
            $trend = 'down';
        } else {
            $trend = 'stable';
        }

        return [
            'annee' => $year,
            'ca' => $ca_current,
            'ca_annee_precedente' => $ca_previous,
            'pourcentage' => round($percentage, 2),
            'tendance' => $trend,
        ];
    }

    public static function topPerformanceByYear($year)
    {
        $totalYear = self::where('annee', $year)->sum('montant_total');

        $top = self::selectRaw('activite_id, SUM(montant_total) as total')
            ->where('annee', $year)
            ->groupBy('activite_id')
            ->orderByDesc('total')
            /*             ->with('LigneBudgetaire')
 */->first();

        if (!$top) {
            return null;
        }

        $percentage = $totalYear > 0
            ? ($top->total / $totalYear) * 100
            : 0;

        return [
            'annee' => $year,
            'activite' => $top->activite?->libelle_activite,
            'montant_total' => $top->total,
            'pourcentage' => round($percentage, 2),
        ];
    }


    public static function getChiffreAffaireParActivite($annee = null)
    {
        $annee = $annee ?? now()->year;

        $data = self::query()
            ->join('dms_activite_agrs as a', 'a.id', '=', 'dms_agr_cotisations.activite_id')
            ->leftJoin('dms_evenements_agrs as e', 'e.id', '=', 'dms_agr_cotisations.evenement_id')
            ->whereYear('dms_agr_cotisations.date_paiement', $annee)
            ->selectRaw('
            a.id as activite_id,
            a.libelle_activite as activite,
            e.id as evenement_id,
            e.libelle_event as evenement,
            SUM(dms_agr_cotisations.montant_total) as total
        ')
            ->groupBy(
                'a.id',
                'a.libelle_activite',
                'e.id',
                'e.libelle_event'
            )
            ->orderByDesc('total')
            ->get();

        $totalGeneral = $data->sum('total');

        $details = $data->map(function ($item) use ($totalGeneral) {
            $item->pourcentage = $totalGeneral > 0
                ? round(($item->total / $totalGeneral) * 100, 2)
                : 0;
            return $item;
        });

        return [
            'annee' => $annee,
            'details' => $details,
            'total_general' => $totalGeneral,
        ];
    }
}
