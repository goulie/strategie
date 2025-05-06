<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Pressing extends Model
{
    protected $table = 'pressings';
    protected $fillable = [
        'name',
        'adresse',
        'logo',
        'localisation'
    ];
}
