<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // Define the relationship with the BudgetCategory model
    public function budgetCategories()
    {
        return $this->hasMany(BudgetCategory::class);
    }

    // Define the relationship with the Guest model
    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    // Define the relationship with the Task model
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
