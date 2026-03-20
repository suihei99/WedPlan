<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetCategory extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'category_name',
        'allocated_amount',
        'spent_amount',
    ];

    protected function casts()
    {
        return [
            'allocated_amount' => 'decimal:2',
            'spent_amount' => 'decimal:2',
        ];
    }

    // Define the relationship with the Couple model
    public function couple()
    {
        return $this->belongsTo(Couple::class, 'user_id');
    }

    // Define the relationship with the Expense model
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
