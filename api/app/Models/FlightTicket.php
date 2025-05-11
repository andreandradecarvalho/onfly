<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightTicket extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'requester_id', 'order_id', 'origin', 'destination', 'departure_date', 'return_date', 'status'];
    /**
     * Usuário associado ao ticket
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
