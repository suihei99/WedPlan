<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        ];
    }


    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
