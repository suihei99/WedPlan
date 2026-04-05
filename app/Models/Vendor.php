<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    // use HasFactory; // Uncomment if you want to use factories for testing
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'contact_number',
        'status',
        'address',
        'business_documents',
    ];

    protected function casts()
    {
        return [
            'status'=> 'boolean',
            'business_documents' => 'string',
        ];
    }


    // Define the relationship with the User model
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with the Servies model
    public function services() : HasMany
    {
        return $this->hasMany(Service::class);
    }
    
    // Define the relationship with the Bookings model
    public function bookings() : HasMany
    {
        return $this->hasMany(Booking::class);
    }

}
