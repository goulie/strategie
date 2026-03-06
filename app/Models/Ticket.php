<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Ticket extends Model
{
    protected $table = 'tickets';
    protected $fillable = [
        'subject',
        'status',
        'priority',
        'assigne_to',
        'user_id',
        'num_ticket',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assigneTo()
    {
        return $this->belongsTo(User::class, 'assigne_to');
    }

    public function messages()
    {
        return $this->hasMany(Conversation::class, 'ticket_id');
    }

    
}
