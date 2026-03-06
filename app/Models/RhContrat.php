<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RhContrat extends Model
{
    // CONTRATS SALARIÉS PRINCIPAUX
    public const CONTRAT_CDI = 'CDI';
    public const CONTRAT_CDD = 'CDD';
    public const CONTRAT_INTERIM = 'INTERIM';
    public const CONTRAT_STAGE = 'STAGE';
    public const CONTRAT_APPRENTISSAGE = 'APPRENTISSAGE';

    // CONTRATS SPÉCIFIQUES
    public const CONTRAT_INSERTION = 'INSERTION';

    // CONTRATS SPÉCIAUX
    public const CONTRAT_FREELANCE = 'FREELANCE';           // Contrat de projet


    public const STATUT_ACTIF = 'ACTIF';
    public const STATUT_INACTIF = 'INACTIF';

    protected $table = 'rh_contrats';
    protected $fillable = [
        'personel_id',
        'code_contrat',
        'type_contrat',
        'date_debut',
        'date_fin',
        'duree',
        'renouvellement',
        'created_at',
        'updated_at',
        'remarques',
        'user_id',
        'statut'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'duree' => 'integer',
        'renouvellement' => 'boolean',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contrat) {

            // Sécurité : ne pas écraser si déjà défini
            if (!empty($contrat->code_contrat)) {
                return;
            }

            $year = now()->year;
            $type = strtoupper($contrat->type_contrat);

            // Dernier numéro pour ce type + année
            $lastNumber = DB::table('rh_contrats')
                ->where('type_contrat', $contrat->type_contrat)
                ->whereYear('created_at', $year)
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(code_contrat, '-', -1) AS UNSIGNED)) as max_num")
                ->value('max_num');

            $nextNumber = str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT);

            $contrat->code_contrat = "{$type}-{$year}-{$nextNumber}";
        });
    }
    /**
     * Relation avec le personnel
     */
    public function personel()
    {
        return $this->belongsTo(RhListePersonnel::class, 'personel_id');
    }

    /**
     * Relation avec l'utilisateur qui a créé le contrat
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function contratTerminaisons()
    {
        return $this->hasMany(RhContratTerminaison::class, 'contrat_id');
    }


    // Dans le modèle RhContrat
    public static function hasContratNonCloture($personelId)
    {
        return self::where('personel_id', $personelId)
            ->where('statut', self::STATUT_ACTIF)
            ->exists();
    }
    /**
     * Liste complète des contrats avec leurs libellés
     */
    public static function getTypesContrats(): array
    {
        return [
            self::CONTRAT_CDI => 'CDI',
            self::CONTRAT_CDD => 'CDD',
            self::CONTRAT_INTERIM => 'INTERIM',
            self::CONTRAT_STAGE => 'STAGE',
            self::CONTRAT_APPRENTISSAGE => 'APPRENTISSAGE',
            self::CONTRAT_INSERTION => 'INSERTION',
            self::CONTRAT_FREELANCE => 'FREELANCE',
        ];
    }

    /**
     * Contrats à durée déterminée (avec date de fin obligatoire)
     */
    public static function getContratsDureeDeterminee(): array
    {
        return [
            self::CONTRAT_CDD,
            self::CONTRAT_INTERIM,
            self::CONTRAT_STAGE,
            self::CONTRAT_APPRENTISSAGE,
            self::CONTRAT_INSERTION,
        ];
    }

    /**
     * Contrats à durée indéterminée (sans date de fin)
     */
    public static function getContratsDureeIndeterminee(): array
    {
        return [
            self::CONTRAT_CDI,
            self::CONTRAT_FREELANCE,
        ];
    }


    /**
     * Contrats de formation / apprentissage
     */
    public static function getContratsFormation(): array
    {
        return [
            self::CONTRAT_STAGE,
            self::CONTRAT_APPRENTISSAGE,
            self::CONTRAT_INSERTION,
        ];
    }

    /**
     * Contrats internationaux
     */
    public static function getContratsInternationaux(): array
    {
        return [
            self::CONTRAT_EXPATRIE,
            self::CONTRAT_DETACHEMENT,
        ];
    }

    /**
     * Contrats de prestation de service
     */
    public static function getContratsPrestation(): array
    {
        return [
            self::CONTRAT_FREELANCE,
            self::CONTRAT_CONSULTANT,
            self::CONTRAT_MISSION,
            self::CONTRAT_PROJET,
        ];
    }

    /**
     * Vérifie si un contrat est à durée déterminée
     */
    public static function isDureeDeterminee(string $typeContrat): bool
    {
        return in_array($typeContrat, self::getContratsDureeDeterminee());
    }

    /**
     * Vérifie si un contrat est à durée indéterminée
     */
    public static function isDureeIndeterminee(string $typeContrat): bool
    {
        return in_array($typeContrat, self::getContratsDureeIndeterminee());
    }

    /**
     * Vérifie si un contrat est de type formation
     */
    public static function isFormation(string $typeContrat): bool
    {
        return in_array($typeContrat, self::getContratsFormation());
    }

    /**
     * Durée maximale par défaut en mois pour chaque type de contrat
     */
    public static function getDureeMaximale(string $typeContrat): ?int
    {
        $durees = [
            self::CONTRAT_CDD => 18,           // CDD max 18 mois avec renouvellement
            self::CONTRAT_INTERIM => 18,       // Intérim max 18 mois
            self::CONTRAT_STAGE => 6,          // Stage max 6 mois
            self::CONTRAT_APPRENTISSAGE => 36, // Apprentissage max 3 ans
            self::CONTRAT_INSERTION => 24,     // Insertion max 2 ans
            self::CONTRAT_MISSION => 36,       // Mission max 3 ans
            self::CONTRAT_PROJET => 36,        // Projet max 3 ans
        ];

        return $durees[$typeContrat] ?? null;
    }

    /**
     * Durée minimale par défaut en mois
     */
    public static function getDureeMinimale(string $typeContrat): ?int
    {
        $durees = [
            self::CONTRAT_CDD => 1,
            self::CONTRAT_INTERIM => 1,
            self::CONTRAT_STAGE => 2,          // Stage min 2 mois
            self::CONTRAT_APPRENTISSAGE => 6,  // Apprentissage min 6 mois
            self::CONTRAT_INSERTION => 6,      // Insertion min 6 mois
            self::CONTRAT_MISSION => 1,
            self::CONTRAT_PROJET => 3,         // Projet min 3 mois
        ];

        return $durees[$typeContrat] ?? 1;
    }

    /**
     * Période d'essai par défaut (en jours)
     */
    public static function getPeriodeEssai(string $typeContrat): ?int
    {
        $periodes = [
            self::CONTRAT_CDI => 90,           // CDI : 3 mois
            self::CONTRAT_CDD => 30,           // CDD : 1 mois
            self::CONTRAT_INTERIM => 7,        // Intérim : 1 semaine
            self::CONTRAT_APPRENTISSAGE => 45, // Apprentissage : 45 jours
            self::CONTRAT_INSERTION => 30,     // Insertion : 1 mois
            self::CONTRAT_EXPATRIE => 90,      // Expatrié : 3 mois
            self::CONTRAT_DETACHEMENT => 90,   // Détachement : 3 mois
        ];

        return $periodes[$typeContrat] ?? null;
    }

    /**
     * Icônes FontAwesome pour chaque type de contrat
     */
    public static function getIcone(string $typeContrat): string
    {
        $icones = [
            self::CONTRAT_CDI => 'fas fa-file-contract',
            self::CONTRAT_CDD => 'fas fa-calendar-alt',
            self::CONTRAT_INTERIM => 'fas fa-exchange-alt',
            self::CONTRAT_STAGE => 'fas fa-graduation-cap',
            self::CONTRAT_APPRENTISSAGE => 'fas fa-tools',
            self::CONTRAT_INSERTION => 'fas fa-handshake',
            self::CONTRAT_FREELANCE => 'fas fa-laptop-code',
            self::CONTRAT_EXPATRIE => 'fas fa-plane',
            self::CONTRAT_DETACHEMENT => 'fas fa-globe-europe',
            self::CONTRAT_BENEVOLAT => 'fas fa-heart',
            self::CONTRAT_CONSULTANT => 'fas fa-chart-line',
            self::CONTRAT_MISSION => 'fas fa-tasks',
            self::CONTRAT_PROJET => 'fas fa-project-diagram',
        ];

        return $icones[$typeContrat] ?? 'fas fa-file-alt';
    }

    /**
     * Couleurs Bootstrap pour chaque type de contrat
     */
    public static function getCouleur(string $typeContrat): string
    {
        $couleurs = [
            self::CONTRAT_CDI => 'success',        // Vert
            self::CONTRAT_CDD => 'warning',        // Orange
            self::CONTRAT_INTERIM => 'info',       // Bleu clair
            self::CONTRAT_STAGE => 'primary',      // Bleu
            self::CONTRAT_APPRENTISSAGE => 'info', // Bleu clair
            self::CONTRAT_INSERTION => 'secondary', // Gris
            self::CONTRAT_FREELANCE => 'dark',     // Noir
            self::CONTRAT_EXPATRIE => 'danger',    // Rouge
            self::CONTRAT_DETACHEMENT => 'purple', // Violet
            self::CONTRAT_BENEVOLAT => 'pink',     // Rose
            self::CONTRAT_CONSULTANT => 'teal',    // Turquoise
            self::CONTRAT_MISSION => 'indigo',     // Indigo
            self::CONTRAT_PROJET => 'orange',      // Orange foncé
        ];

        return $couleurs[$typeContrat] ?? 'secondary';
    }

    /**
     * Libellé formaté avec badge Bootstrap
     */
    public static function getLibelleAvecBadge(string $typeContrat): string
    {
        $libelles = self::getTypesContrats();
        $libelle = $libelles[$typeContrat] ?? 'Type inconnu';
        $couleur = self::getCouleur($typeContrat);
        $icone = self::getIcone($typeContrat);

        return sprintf(
            '<span class="badge badge-%s"><i class="%s mr-1"></i> %s</span>',
            $couleur,
            $icone,
            $libelle
        );
    }

    /**
     * Options pour les select HTML groupés
     */
    public static function getOptionsGroupees(): array
    {
        return [
            'Contrats salariés' => [
                self::CONTRAT_CDI => self::getTypesContrats()[self::CONTRAT_CDI],
                self::CONTRAT_CDD => self::getTypesContrats()[self::CONTRAT_CDD],
                self::CONTRAT_INTERIM => self::getTypesContrats()[self::CONTRAT_INTERIM],
            ],
            'Contrats de formation' => [
                self::CONTRAT_STAGE => self::getTypesContrats()[self::CONTRAT_STAGE],
                self::CONTRAT_APPRENTISSAGE => self::getTypesContrats()[self::CONTRAT_APPRENTISSAGE],
                self::CONTRAT_INSERTION => self::getTypesContrats()[self::CONTRAT_INSERTION],
            ],
            'Contrats internationaux' => [
                self::CONTRAT_EXPATRIE => self::getTypesContrats()[self::CONTRAT_EXPATRIE],
                self::CONTRAT_DETACHEMENT => self::getTypesContrats()[self::CONTRAT_DETACHEMENT],
            ],
            'Prestations de service' => [
                self::CONTRAT_FREELANCE => self::getTypesContrats()[self::CONTRAT_FREELANCE],
                self::CONTRAT_CONSULTANT => self::getTypesContrats()[self::CONTRAT_CONSULTANT],
                self::CONTRAT_MISSION => self::getTypesContrats()[self::CONTRAT_MISSION],
                self::CONTRAT_PROJET => self::getTypesContrats()[self::CONTRAT_PROJET],
            ],
            'Autres' => [
                self::CONTRAT_BENEVOLAT => self::getTypesContrats()[self::CONTRAT_BENEVOLAT],
            ],
        ];
    }

    /**
     * Accessor pour le libellé du type de contrat
     */
    public function getTypeContratLibelleAttribute(): string
    {
        return self::getTypesContrats()[$this->type_contrat] ?? 'Inconnu';
    }

    /**
     * Accessor pour le badge du type de contrat
     */
    public function getTypeContratBadgeAttribute(): string
    {
        return self::getLibelleAvecBadge($this->type_contrat);
    }

    /**
     * Accessor pour l'icône du contrat
     */
    public function getIconeAttribute(): string
    {
        return self::getIcone($this->type_contrat);
    }

    /**
     * Accessor pour vérifier si le contrat est expiré
     */
    public function getEstExpireAttribute(): bool
    {
        if (!$this->date_fin || self::isDureeIndeterminee($this->type_contrat)) {
            return false;
        }

        return now()->greaterThan($this->date_fin);
    }

    /**
     * Accessor pour vérifier si le contrat est à renouveler (expire dans moins de 30 jours)
     */
    public function getARenouvelerAttribute(): bool
    {
        if (!$this->date_fin || self::isDureeIndeterminee($this->type_contrat)) {
            return false;
        }

        $dateExpiration = \Carbon\Carbon::parse($this->date_fin);
        return now()->diffInDays($dateExpiration, false) <= 30;
    }

    /**
     * Scope pour les contrats à durée déterminée
     */
    public function scopeDureeDeterminee($query)
    {
        return $query->whereIn('type_contrat', self::getContratsDureeDeterminee());
    }

    /**
     * Scope pour les contrats à durée indéterminée
     */
    public function scopeDureeIndeterminee($query)
    {
        return $query->whereIn('type_contrat', self::getContratsDureeIndeterminee());
    }

    /**
     * Scope pour les contrats actifs (non expirés)
     */
    public function scopeActifs($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('date_fin')
                ->orWhere('date_fin', '>=', now());
        });
    }

    /**
     * Scope pour les contrats expirés
     */
    public function scopeExpires($query)
    {
        return $query->whereNotNull('date_fin')
            ->where('date_fin', '<', now());
    }

    public function scopeActif(Builder $query)
    {
        $today = Carbon::today()->toDateString();

        return $query
            ->where('date_debut', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('date_fin')
                    ->orWhere('date_fin', '>=', $today);
            });
    }

    /**
     * Vérifie si un collaborateur a un contrat actif
     */
    public static function hasContratActif(int $personelId): bool
    {
        return self::where('personel_id', $personelId)
            ->actif()
            ->exists();
    }

    /**
     * Récupère le contrat actif du collaborateur
     */
    public static function getContratActif(int $personelId): ?self
    {
        return self::where('personel_id', $personelId)
            ->actif()
            ->first();
    }
}
