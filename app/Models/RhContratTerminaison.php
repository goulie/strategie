<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RhContratTerminaison extends Model
{
    protected $table = 'rh_contrat_terminaisons';

    protected $fillable = [
        'contrat_id',
        'date_termination',
        'motif',
        'initiative',
        'commentaire',
        'documents',
        'user_id',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check() && empty($model->user_id)) {
                $model->user_id = Auth::id();
            }
        });
    }
    /* =====================================================
     | CONSTANTES METIER – MOTIFS DE TERMINAISON
     |===================================================== */

    public const MOTIF_FIN_CONTRAT           = 'FIN_CONTRAT';           // Terme normal
    public const MOTIF_DEMISSION             = 'DEMISSION';             // Initiative salarié
    public const MOTIF_LICENCIEMENT           = 'LICENCIEMENT';          // Initiative employeur
    public const MOTIF_RUPTURE_CONVENTIONNELLE = 'RUPTURE_CONVENTIONNELLE';
    public const MOTIF_FAUTE_GRAVE            = 'FAUTE_GRAVE';
    public const MOTIF_ABANDON_POSTE          = 'ABANDON_POSTE';
    public const MOTIF_INAPTITUDE             = 'INAPTITUDE';
    public const MOTIF_FORCE_MAJEURE          = 'FORCE_MAJEURE';

    /**
     * Liste des motifs disponibles
     */
    public const MOTIFS = [
        self::MOTIF_FIN_CONTRAT,
        self::MOTIF_DEMISSION,
        self::MOTIF_LICENCIEMENT,
        self::MOTIF_RUPTURE_CONVENTIONNELLE,
        self::MOTIF_FAUTE_GRAVE,
        self::MOTIF_ABANDON_POSTE,
        self::MOTIF_INAPTITUDE,
        self::MOTIF_FORCE_MAJEURE,
    ];

    /* =====================================================
     | CONSTANTES METIER – INITIATIVE
     |===================================================== */

    public const INITIATIVE_EMPLOYEUR = 'EMPLOYEUR';
    public const INITIATIVE_EMPLOYE   = 'EMPLOYE';
    public const INITIATIVE_COMMUNE   = 'COMMUNE';

    /**
     * Liste des initiatives possibles
     */
    public const INITIATIVES = [
        self::INITIATIVE_EMPLOYEUR,
        self::INITIATIVE_EMPLOYE,
        self::INITIATIVE_COMMUNE,
    ];

    /* =====================================================
     | RELATIONS
     |===================================================== */

    public function contrat()
    {
        return $this->belongsTo(RhContrat::class, 'contrat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
