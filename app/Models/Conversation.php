<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Conversation extends Model
{
    protected $table = 'conversations';
    protected $fillable = ['ticket_id','message','send_by','order','response_time']; 

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'send_by');
    }
   
    
}
