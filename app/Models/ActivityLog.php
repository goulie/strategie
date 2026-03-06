<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ActivityLog extends Model
{

    protected $table = 'activity_logs';
    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'old_values',
        'new_values',
        'user_id'
    ];

    
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
