<?php

namespace App\Models;

use App\Traits\RH\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RhListePersonnel extends Model
{
    use HasActivityLog;

    const CONTRAT_status_ACTIVE = 'Actif';
    const CONTRAT_status_DEMISSION = 'Démission';
    const CONTRAT_status_SUSPENSION = 'Suspension';


    protected $table = 'rh_liste_personnels';
    /* matricule	varchar	YES			
nom	varchar	YES			
nrenoms	varchar	YES			
sexe	varchar	YES			
date_naissance	date	YES			
lieu_naissance	varchar	YES			
nationalite	varchar	YES			
cni_passeport	varchar	YES			
situation_matrimoniale	varchar	YES			
contact_personnel	varchar	YES			
email	varchar	YES			
nom_complet_personne_urgence	varchar	YES			
service_id	bigint	YES			
site_travail	varchar	YES			
qualification	varchar	YES			
date_entree	date	YES			
num_CNPS	varchar	YES			
num_CMU	varchar	YES			
status	varchar	YES			
commentaire	text	YES					
adresse	text	YES			
poste	varchar	YES			
contact_personne_urgence	varchar	YES			
nom_complet_conjoint	varchar	YES			
extrait_conjoint	varchar	YES			
lien_personne_urgence	varchar	YES			
autre_lien_personne_urgence	varchar	YES			
extrait_naissance */
    protected $fillable = [
        'matricule',
        'nom',
        'prenoms',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'cni_passeport',
        'situation_matrimoniale',
        'contact_personnel',
        'email',
        'nom_complet_personne_urgence',
        'service_id',
        'site_travail',
        'qualification',
        'date_entree',
        'num_CNPS',
        'num_CMU',
        'status',
        'commentaire',
        'adresse',
        'poste',
        'contact_personne_urgence',
        'nom_complet_conjoint',
        'extrait_conjoint',
        'lien_personne_urgence',
        'autre_lien_personne_urgence',
        'extrait_naissance',
    ];



    public function services()
    {
        return $this->hasMany(RhService::class, 'departement_id');
    }

    public function contrats()
    {
        return $this->hasMany(RhContrat::class, 'personel_id');
    }
    public function getPersonnelsCountAttribute()
    {
        if ($this->relationLoaded('services')) {
            return $this->services->sum(function ($service) {
                return $service->rh_listes_count
                    ?? $service->rhListes()->count();
            });
        }

        return RhListePersonnel::whereIn(
            'service_id',
            $this->services()->pluck('id')
        )->count();
    }

    public function scopeWithPersonnelCount($query)
    {
        return $query->with([
            'services' => function ($q) {
                $q->withCount('rhListes');
            }
        ]);
    }

    public static function totalPersonnel()
    {
        return self::where('status', 'Actif')->count();
    }

    public static function personnelParService()
    {
        return self::select('service_id')->where('status', 'Actif')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('service_id')
            ->with('service')
            ->get();
    }

    public static function personnelParDirection()
    {
        return self::join('rh_services', 'rh_liste_personnels.service_id', '=', 'rh_services.id')
            ->join('rh_directions', 'rh_services.departement_id', '=', 'rh_directions.id')
            ->select(
                'rh_directions.libelle_directions',
                DB::raw('COUNT(rh_liste_personnels.id) as total')
            )->where('rh_liste_personnels.status', 'Actif')
            ->groupBy('rh_directions.libelle_directions')
            ->get();
    }



    public static function personnelParSexe()
    {
        return self::where('status', 'Actif')->select('Sexe')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('Sexe')
            ->get();
    }

    public static function personnelParTypeContrat()
    {
        return self::join('rh_contrats', 'rh_liste_personnels.id', '=', 'rh_contrats.personel_id')->where('rh_liste_personnels.status', 'Actif')->select('type_contrat')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('rh_contrats.type_contrat')
            ->get();
    }

    public static function personnelParStatut()
    {
        return self::where('status', 'Actif')->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->get();
    }

    public static function contratsExpirant($jours = 30)
    {
        return self::join('rh_contrats', 'rh_liste_personnels.id', '=', 'rh_contrats.personel_id')
            ->where('rh_liste_personnels.status', 'Actif')
            ->whereNotNull('date_fin')
            ->whereBetween('date_fin', [now(), now()->addDays($jours)])
            ->get();
    }

    public static function personnelParSite()
    {
        return self::where('status', 'Actif')->select('site_travail')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('site_travail')
            ->get();
    }
}
