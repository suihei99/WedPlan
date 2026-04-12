<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    // use HasFactory; // Uncomment if you want to use factories for testing
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'couple_id',
        'type_service',
        'booking_date',
        'status',
        'notes',
    ];

    // cast attributes to appropriate data types
    protected function casts()
    {
        return [
            'booking_date' => 'date',
            'status' => 'boolean',
            'notes' => 'string',
        ];
    }

    // Define the relationship with the vendor user
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship with the couple user
    public function couple(): BelongsTo
    {
        return $this->belongsTo(User::class, 'couple_id');
    }
}
