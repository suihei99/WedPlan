<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Couple extends Model
{
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'partner_1_name',
        'partner_2_name',
        'wedding_date',
        'wedding_venue',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship with the BudgetCategory model
    public function budgetCategories(): HasMany
    {
        return $this->hasMany(BudgetCategory::class, 'user_id', 'user_id');
    }

    // Define the relationship with the Guest model
    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class, 'user_id', 'user_id');
    }

    // Define the relationship with the Task model
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'user_id', 'user_id');
    }

    // Accessor to calculate the total budget spent across all categories
    // Total spent across all categories
    public function getTotalBudgetSpentAttribute(): float
    {
        return (float) $this->budgetCategories->flatMap->expenses->sum('amount');
    }

    // Accessor to calculate the remaining budget
    public function getRemainingBudgetAttribute(): float
    {
        return $this->total_budget_limit - $this->total_budget_spent;
    }
}
