<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PressingUser extends Model
{
    protected $table = 'pressing_users';
    protected $fillable = [
        'id_pressing',
        'id_user',
        'statut'
    ];
}
