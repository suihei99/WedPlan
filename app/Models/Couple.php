<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Couple extends Model
{
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'partner1_name',
        'partner2_name',
        'wedding_date',
        'wedding_location',
        'wedding_time',
        'total_budget_limit',
    ];

    protected function casts()
    {
        return [
            'wedding_date' => 'date',
            'wedding_time' => 'time',
            'total_budget_limit' => 'decimal:2',
        ];
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
