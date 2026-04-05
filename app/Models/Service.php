<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    // use HasFactory; // Uncomment if you want to use factories for testing
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'service_name',
        'type_service',
        'price_estimate',
        'description',
        'image_url',
    ];

    // cast attributes to appropriate data types
    protected function casts()
    {
        return [
            'price_estimate' => 'decimal:2',
            'description' => 'string',
            'image_url' => 'string',
        ];
    }

    // Define the relationship with the User model
    public function vendor() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
