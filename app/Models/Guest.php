<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guest extends Model
{
    // --- IGNORE ---
    use HasFactory, Notifiable;

    // The attributes that are mass assignable.
    protected $fillable = [
        'user_id',
        'name',
        'pax_count',
        'phone',
        'rsvp_status',
        'qr_code_string',
        'invite_code', 
    ];

    //RSVP status constants
    const RSVP_PENDING = 'pending';
    const RSVP_CONFIRMED = 'confirmed';
    const RSVP_DECLINED = 'declined';
    const RSVP_STATUS = [self::RSVP_PENDING, self::RSVP_CONFIRMED, self::RSVP_DECLINED];

    // Define the relationship with the couple model
    public function couple() : BelongsTo
    {
        return $this->belongsTo(Couple::class, 'user_id');
    }

    public function isCheckedIn() : bool
    {
        return $this->rsvp_status === self::RSVP_CONFIRMED;
    }
}
