<?php

namespace App\Models;

use App\Traits\RH\HasActivityLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DmsCotisationMembre extends Model
{
    use HasActivityLog;
    protected $table = 'dms_cotisation_membres';

    protected $fillable = [
        'montant',
        'date_paiement',
        'membre_id',
        'mode_paiement',
        'observation',
        'created_at',
        'updated_at',
        'user_id',
        'date_echeance',
        'annee_cotisation',
        'reste_a_payer',
        'status',
        'reference'
    ];

    protected $dates = [
        'date_paiement',
        'date_echeance',
        'created_at',
        'updated_at'
    ];

    protected $appends = [
        'est_active',
        'est_expiree',
        'jours_restants'
    ];

    // Relations
    public function membre()
    {
        return $this->belongsTo(DmsMembre::class, 'membre_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('date_echeance', '>=', Carbon::now()->format('Y-m-d'));
    }

    public function scopeExpiree(Builder $query)
    {
        return $query->where('date_echeance', '<', Carbon::now()->format('Y-m-d'));
    }

    public function scopePourAnnee(Builder $query, $annee)
    {
        return $query->where('annee_cotisation', $annee);
    }

    public static function PourAnnee($annee)
    {
        return self::where('annee_cotisation', $annee);
    }

    public function scopePourMembre(Builder $query, $membre_id)
    {
        return $query->where('membre_id', $membre_id);
    }

    // Accessors
    public function getEstActiveAttribute()
    {
        if (!$this->date_echeance) {
            return false;
        }
        return Carbon::now()->lte(Carbon::parse($this->date_echeance));
    }

    public function getEstExpireeAttribute()
    {
        return !$this->est_active;
    }

    public function getJoursRestantsAttribute()
    {
        if (!$this->date_echeance || !$this->est_active) {
            return 0;
        }

        return Carbon::now()->diffInDays(Carbon::parse($this->date_echeance), false);
    }

    public function getDatePaiementFormattedAttribute()
    {
        return $this->date_paiement ? Carbon::parse($this->date_paiement)->format('d/m/Y') : null;
    }

    public function getDateEcheanceFormattedAttribute()
    {
        return $this->date_echeance ? Carbon::parse($this->date_echeance)->format('d/m/Y') : null;
    }

    // Boot
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = auth()->check() ? auth()->user()->id : null;

            // Calcul automatique de la date d'échéance si non fournie
            if ($model->annee_cotisation && !$model->date_echeance) {
                $model->date_echeance = Carbon::create($model->annee_cotisation + 1, 3, 31);
            }
        });

        static::saving(function ($model) {
            // S'assurer que l'année de cotisation est cohérente avec la date de paiement
            if ($model->date_paiement && !$model->annee_cotisation) {
                $model->annee_cotisation = Carbon::parse($model->date_paiement)->year;
            }

            // Calculer la date d'échéance si l'année de cotisation change
            if ($model->annee_cotisation && (!$model->date_echeance || $model->isDirty('annee_cotisation'))) {
                $model->date_echeance = Carbon::create($model->annee_cotisation + 1, 3, 31);
            }
        });
    }

    // Méthodes pour un membre spécifique
    public static function getCotisationsMembre($membre_id, $annee = null)
    {
        $query = self::pourMembre($membre_id)->active();

        if ($annee) {
            $query->pourAnnee($annee);
        }

        return $query->get();
    }

    public static function estMembreAJour($membre_id, $annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return self::pourMembre($membre_id)
            ->pourAnnee($annee)
            ->active()
            ->exists();
    }

    public static function getDerniereCotisationMembre($membre_id)
    {
        return self::pourMembre($membre_id)
            ->active()
            ->orderByDesc('date_echeance')
            ->first();
    }

    // Méthodes statistiques générales
    public static function totalCotisationsMois($annee = null, $mois = null)
    {
        $annee = $annee ?? Carbon::now()->year;
        $mois = $mois ?? Carbon::now()->month;

        return self::whereYear('date_paiement', $annee)
            ->whereMonth('date_paiement', $mois)
            ->active()
            ->sum('montant');
    }

    public static function nbMembresCotisants($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return self::pourAnnee($annee)
            ->active()
            ->distinct('membre_id')
            ->count('membre_id');
    }

    public static function nbCotisations($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return self::pourAnnee($annee)
            ->active()
            ->count();
    }


    public static function totalCotisationsParMoisAnneeEnCours($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;


        $resultats = self::select(
            DB::raw('MONTH(date_paiement) as mois'),
            DB::raw('SUM(montant) as total')
        )
            ->whereYear('date_paiement', $annee)
            ->groupBy(DB::raw('MONTH(date_paiement)'))
            ->get()
            ->pluck('total', 'mois');

        // Initialiser les 12 mois à 0
        $totauxParMois = array_fill(1, 12, 0);

        foreach ($resultats as $mois => $total) {
            $totauxParMois[$mois] = round($total / 1_000_000, 2); // Millions FCFA
        }

        return array_values($totauxParMois);
    }

    //Pour graphique
    public static function getMontantsParMois(?int $annee = null): array
    {
        $annee = $annee ?? Carbon::now()->year;

        $resultats = self::select(
            DB::raw('MONTH(date_paiement) as mois'),
            DB::raw('SUM(montant) as total')
        )
            ->whereNotNull('date_paiement')
            ->where('annee_cotisation', $annee)
            ->groupBy(DB::raw('MONTH(date_paiement)'))
            ->orderBy(DB::raw('MONTH(date_paiement)'))
            ->get()
            ->pluck('total', 'mois');

        // Initialiser les 12 mois à 0
        $totauxParMois = array_fill(1, 12, 0);

        foreach ($resultats as $mois => $total) {
            // Conversion en Millions FCFA
            $totauxParMois[$mois] = round($total / 1_000_000, 2);
        }

        // Highcharts attend un tableau indexé 0 → 11
        return array_values($totauxParMois);
    }

    public static function totalCotisationsParMoisPourAnnee($annee)
    {
        return self::totalCotisationsParMoisAnneeEnCours($annee);
    }

    public static function totalCotisationsAnnuelles($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return self::where('annee_cotisation', $annee)
            //->active()
            ->sum('montant');
    }

    public static function membresAJour($annee = null)
    {
        return self::nbMembresCotisants($annee);
    }

    public static function nouveauxMembresAnnuels($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return self::join('dms_membres', 'dms_cotisation_membres.membre_id', '=', 'dms_membres.id')
            ->whereYear('dms_membres.date_adhesion', $annee)
            ->where('dms_cotisation_membres.annee_cotisation', $annee)
            ->distinct('dms_cotisation_membres.membre_id')
            ->count('dms_cotisation_membres.membre_id');
    }
    public static function nouveauxMembresAnnuelsCotisations($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return self::join('dms_membres', 'dms_cotisation_membres.membre_id', '=', 'dms_membres.id')
            ->whereYear('dms_membres.date_adhesion', $annee)
            ->where('dms_cotisation_membres.annee_cotisation', $annee)
            ->groupBy('dms_cotisation_membres.membre_id')
            ->selectRaw('SUM(dms_cotisation_membres.montant) as total')
            ->get()
            ->sum('total');
    }

    public static function membresAJourStats($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        $data = self::where('annee_cotisation', $annee)
            ->whereDate('date_echeance', '>=', Carbon::today())
            ->selectRaw('
            COUNT(DISTINCT membre_id) as nombre,
            SUM(montant) as montant
        ')
            ->first();

        return [
            'nombre'  => (int) $data->nombre,
            'montant' => (float) $data->montant,
        ];
    }


    public static function nouveauxMembresMois($annee = null, $mois = null)
    {
        $annee = $annee ?? Carbon::now()->year;
        $mois = $mois ?? Carbon::now()->month;

        return self::join('dms_membres', 'dms_cotisation_membres.membre_id', '=', 'dms_membres.id')
            ->whereYear('dms_cotisation_membres.date_paiement', $annee)
            ->whereMonth('dms_cotisation_membres.date_paiement', $mois)
            ->where('dms_cotisation_membres.date_echeance', '>=', Carbon::now()->format('Y-m-d'))
            ->distinct('dms_cotisation_membres.membre_id')
            ->count('dms_cotisation_membres.membre_id');
    }

    public static function tauxRecouvrement($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        $totalMembres = DmsMembre::count();

        if ($totalMembres === 0) {
            return 0;
        }

        $membresAJour = self::membresAJour($annee);

        return round(($membresAJour / $totalMembres) * 100, 1);
    }



    // Ancienne méthode maintenue pour compatibilité
    public static function MembreNonAjour($annee = null)
    {
        return self::membresNonAJour($annee);
    }

    // Méthodes avancées de reporting
    public static function statsParMoisEtLigneBudgetaire($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return self::select(
            DB::raw('YEAR(dms_cotisation_membres.date_paiement) as annee'),
            DB::raw('MONTH(dms_cotisation_membres.date_paiement) as mois'),
            'dms_ligne_budgetaires.libelle_ligne',
            DB::raw('SUM(dms_cotisation_membres.montant) as montant_total'),
            DB::raw('COUNT(DISTINCT dms_cotisation_membres.membre_id) as nb_membres'),
            DB::raw('COUNT(dms_cotisation_membres.id) as nb_cotisations')
        )
            ->join('dms_membres', 'dms_cotisation_membres.membre_id', '=', 'dms_membres.id')
            ->join('dms_ligne_budgetaires', 'dms_membres.linge_budgetaire_id', '=', 'dms_ligne_budgetaires.id')
            ->whereYear('dms_cotisation_membres.annee_cotisation', $annee)
            ->where('dms_cotisation_membres.date_echeance', '>=', Carbon::now()->format('Y-m-d'))
            ->groupBy(
                DB::raw('YEAR(dms_cotisation_membres.date_paiement)'),
                DB::raw('MONTH(dms_cotisation_membres.date_paiement)'),
                'dms_ligne_budgetaires.libelle_ligne'
            )
            ->orderBy('annee')
            ->orderBy('mois')
            ->orderBy('dms_ligne_budgetaires.libelle_ligne')
            ->get();
    }

    public static function statsParLigneBudgetaireEtMois($year = null)
    {
        $year = $year ?? Carbon::now()->year;

        return self::select(
            'dms_ligne_budgetaires.id as ligne_id',
            'dms_ligne_budgetaires.libelle_ligne',
            'dms_module_recettes.libelle_module',
            'dms_module_recettes.ligne_budgetaire',
            DB::raw('MONTH(dms_cotisation_membres.date_paiement) as mois'),
            DB::raw('SUM(dms_cotisation_membres.montant) as total'),
            DB::raw('COUNT(DISTINCT dms_cotisation_membres.membre_id) as nb_membres')
        )
            ->join('dms_membres', 'dms_cotisation_membres.membre_id', '=', 'dms_membres.id')
            ->join('dms_ligne_budgetaires', 'dms_membres.linge_budgetaire_id', '=', 'dms_ligne_budgetaires.id')
            ->join('dms_module_recettes', 'dms_ligne_budgetaires.code_budgetaire_id', '=', 'dms_module_recettes.id')
            ->whereYear('dms_cotisation_membres.date_paiement', $year)
            ->where('dms_cotisation_membres.date_echeance', '>=', Carbon::now()->format('Y-m-d'))
            ->groupBy(
                'dms_ligne_budgetaires.id',
                'dms_ligne_budgetaires.libelle_ligne',
                'dms_module_recettes.libelle_module',
                'dms_module_recettes.ligne_budgetaire',
                DB::raw('MONTH(dms_cotisation_membres.date_paiement)')
            )
            ->get();
    }

    // Méthodes d'analyse
    public static function getCotisationsExpirees()
    {
        return self::expiree()->get();
    }

    public static function getCotisationsExpirantBientot($jours = 30)
    {
        return self::where('date_echeance', '>=', Carbon::now()->format('Y-m-d'))
            ->where('date_echeance', '<=', Carbon::now()->addDays($jours)->format('Y-m-d'))
            ->get();
    }

    public static function getCotisationsParPeriode($debut, $fin)
    {
        return self::whereBetween('date_paiement', [$debut, $fin])
            ->active()
            ->get();
    }

    public static function getMembresAvecCotisationExpiree()
    {
        return DmsMembre::whereHas('cotisations', function ($query) {
            $query->where('date_echeance', '<', Carbon::now()->format('Y-m-d'));
        })->get();
    }

    public static function getMembresSansCotisationActive()
    {
        return DmsMembre::whereDoesntHave('cotisations', function ($query) {
            $query->where('date_echeance', '>=', Carbon::now()->format('Y-m-d'));
        })->get();
    }

    // Méthodes de synthèse
    public static function getSyntheseAnnuelle($annee = null)
    {
        $annee = $annee ?? Carbon::now()->year;

        return [
            'annee' => $annee,
            'total_cotisations' => self::totalCotisationsAnnuelles($annee),
            'nombre_membres_cotisants' => self::membresAJour($annee),
            'nombre_cotisations' => self::nbCotisations($annee),
            'nouveaux_membres' => self::nouveauxMembresAnnuels($annee),
            'taux_recouvrement' => self::tauxRecouvrement($annee),
            'membres_non_a_jour' => self::membresNonAJour($annee),
            'cotisations_par_mois' => self::totalCotisationsParMoisAnneeEnCours($annee)
        ];
    }

    public static function getEvolutionSur5Ans()
    {
        $currentYear = Carbon::now()->year;
        $evolution = [];

        for ($i = 4; $i >= 0; $i--) {
            $annee = $currentYear - $i;
            $evolution[] = [
                'annee' => $annee,
                'total' => self::totalCotisationsAnnuelles($annee),
                'membres' => self::membresAJour($annee),
                'taux' => self::tauxRecouvrement($annee)
            ];
        }

        return $evolution;
    }

    // Validation personnalisée
    public function validerCotisation()
    {
        // Vérifier si une cotisation existe déjà pour ce membre cette année
        $existe = self::pourMembre($this->membre_id)
            ->pourAnnee($this->annee_cotisation)
            ->active()
            ->where('id', '!=', $this->id)
            ->exists();

        if ($existe) {
            throw new \Exception("Ce membre a déjà une cotisation active pour l'année " . $this->annee_cotisation);
        }

        // Vérifier que la date d'échéance n'est pas passée
        if ($this->date_echeance && Carbon::parse($this->date_echeance)->isPast()) {
            throw new \Exception("La date d'échéance est déjà passée");
        }

        return true;
    }

    public static function getCotisationsParTypePlanAdhesion($annee)
    {
        $annee = $annee ?? Carbon::now()->year;

        return self::select(
            'dms_plan_adhesions.type_plan_adhesion',
            DB::raw('COUNT(DISTINCT dms_cotisation_membres.membre_id) as total_membres'),
            DB::raw('SUM(dms_cotisation_membres.montant) as total_montant'),
            DB::raw('COUNT(dms_cotisation_membres.id) as nombre_cotisations')
        )
            ->join('dms_membres', 'dms_cotisation_membres.membre_id', '=', 'dms_membres.id')
            ->join('dms_plan_adhesions', 'dms_membres.plan_adhesion_id', '=', 'dms_plan_adhesions.id')
            ->where('dms_cotisation_membres.annee_cotisation', $annee)
            //->where('dms_cotisation_membres.date_echeance', '>=', Carbon::now()->format('Y-m-d'))
            ->groupBy('dms_plan_adhesions.type_plan_adhesion')
            ->orderBy('dms_plan_adhesions.type_plan_adhesion')
            ->get();
    }


    public static function getCotisationsParMembre($membre_id)
    {
        return self::where('membre_id', $membre_id)->get();
    }

    //retrouver les cotisations par plan adhésion et nombre de membres cotisants
    public static function getStatsParAnneeEtTypePlan($annee, $typePlanAdhesion)
    {
        $typesValides = DmsPlanAdhesion::PLAN_TYPES;
        if (!in_array($typePlanAdhesion, $typesValides)) {
            throw new \InvalidArgumentException("Type de plan d'adhésion invalide. Valeurs acceptées: " . implode(', ', $typesValides));
        }

        $stats = self::select(
            'dms_plan_adhesions.id',
            'dms_plan_adhesions.title_plan',
            'dms_plan_adhesions.type_plan_adhesion',
            DB::raw('COUNT(DISTINCT dms_cotisation_membres.membre_id) as nombre_membres_cotisants'),
            DB::raw('SUM(dms_cotisation_membres.montant) as montant_total'),
            DB::raw('COUNT(dms_cotisation_membres.id) as nombre_cotisations')
        )
            ->join('dms_membres', 'dms_cotisation_membres.membre_id', '=', 'dms_membres.id')
            ->join('dms_plan_adhesions', 'dms_membres.plan_adhesion_id', '=', 'dms_plan_adhesions.id')
            ->where('dms_cotisation_membres.annee_cotisation', $annee)
            ->where('dms_plan_adhesions.type_plan_adhesion', $typePlanAdhesion)
            //->where('dms_cotisation_membres.date_echeance', '>=', Carbon::now()->format('Y-m-d'))
            ->groupBy(
                'dms_plan_adhesions.id',
                'dms_plan_adhesions.title_plan',
                'dms_plan_adhesions.type_plan_adhesion'
            )
            ->orderBy('dms_plan_adhesions.title_plan')
            ->get();

        // Calcul du total global pour ce type de plan
        $totalGlobal = [
            'montant_total' => $stats->sum('montant_total'),
            'nombre_membres_cotisants' => $stats->sum('nombre_membres_cotisants'),
            'nombre_cotisations' => $stats->sum('nombre_cotisations')
        ];

        // Transformation des résultats en tableau structuré
        $resultats = [];
        foreach ($stats as $stat) {
            $resultats[] = [
                'plan_id' => $stat->id,
                'title_plan' => $stat->title_plan,
                'type_plan_adhesion' => $stat->type_plan_adhesion,
                'nombre_membres_cotisants' => (int) $stat->nombre_membres_cotisants,
                'montant_total' => (float) $stat->montant_total,
                'nombre_cotisations' => (int) $stat->nombre_cotisations,
                'moyenne_par_membre' => $stat->nombre_membres_cotisants > 0
                    ? round($stat->montant_total / $stat->nombre_membres_cotisants, 2)
                    : 0
            ];
        }

        return [
            'annee' => $annee,
            'type_plan_adhesion' => $typePlanAdhesion,
            'total_global' => $totalGlobal,
            'plans' => $resultats
        ];
    }

    public static function MembresNonAJour($annee)
    {
        $annee = $annee ?? Carbon::now()->year;
        $today = Carbon::today();

        $subQuery = self::selectRaw('MAX(id) as id')
            ->groupBy('membre_id');

        $data = self::whereIn('id', $subQuery)
            ->whereDate('date_echeance', '<', $today)
            ->selectRaw('
                COUNT(DISTINCT membre_id) as nombre,
                SUM(montant) as montant
            ')
            ->first();

        return [
            'nombre'  => (int) $data->nombre,
            'montant' => (float) $data->montant,
            'data'    => self::whereDate('date_echeance', '<', $today)->get()
        ];
    }

    public static function MembresExpires()
    {
        $dateLimite = Carbon::now()->subYears(3);

        $sub = DB::table('dms_cotisation_membres')
            ->select('membre_id', DB::raw('MAX(date_paiement) as last_payment'))
            ->groupBy('membre_id');

        return DB::table('dms_membres as m')
            ->leftJoinSub($sub, 'c', function ($join) {
                $join->on('m.id', '=', 'c.membre_id');
            })
            ->where(function ($query) use ($dateLimite) {
                $query->whereNull('c.last_payment') 
                    ->orWhere('c.last_payment', '<=', $dateLimite);
            })
            ->select('m.*')
            ->get();
    }
}
