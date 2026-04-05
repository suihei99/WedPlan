<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;


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
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship with the BudgetCategory model
    public function budgetCategories() : HasMany
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

    // Accessor to calculate the total budget spent across all categories
    // Total spent across all categories
    public function getTotalBudgetSpentAttribute() : float
    {
        return $this->budgetCategories->flatMap->expenses->sum('amount_spent');
    }

    // Accessor to calculate the remaining budget
    public function getRemainingBudgetAttribute() : float
    {
        return $this->total_budget_limit - $this->total_budget_spent;
    }

}
