<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Guest extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'name',
        'pax_count',
        'phone',
        'rsvp_status',
        'qr_code_string',
        'invite_code', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
