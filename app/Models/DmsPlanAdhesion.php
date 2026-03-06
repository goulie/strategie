<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DmsPlanAdhesion extends Model
{
    public const PLAN_ADHESION_ACTIF = 'ACTIF';
    public const PLAN_ADHESION_AFFILIE = 'AFFILIE';
    public const PLAN_ADHESION_INDIVIDUEL = 'INDIVIDUEL';

    public const PLAN_TYPES = [
        self::PLAN_ADHESION_ACTIF => self::PLAN_ADHESION_ACTIF,
        self::PLAN_ADHESION_AFFILIE => self::PLAN_ADHESION_AFFILIE,
        self::PLAN_ADHESION_INDIVIDUEL => self::PLAN_ADHESION_INDIVIDUEL
    ];

    protected $table = 'dms_plan_adhesions';
    protected $fillable = ['title_plan', 'description_plan', 'price_plan', 'price_xof', 'type_plan_adhesion'];

    // App\Models\DmsPlanAdhesion.php
public function getPlanTypes(){
    return self::PLAN_TYPES;
}
    public function membres()
    {
        return $this->hasMany(DmsMembre::class, 'plan_adhesion_id');
    }
}
