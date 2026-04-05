<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetCategory extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'category_name',
        'allocated_amount',
    ];

    protected function casts()
    {
        return [
            'allocated_amount' => 'decimal:2',
            'spent_amount' => 'decimal:2',
        ];
    }

    // Define the relationship with the Couple model
    public function couple() : BelongsTo
    {
        return $this->belongsTo(Couple::class, 'user_id');
    }

    // Define the relationship with the Expense model
    public function expenses() : HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // Accessor to calculate the total amount spent in this budget category
    public function getTotalSpentAttribute() : float
    {
        return (float) $this->expenses()->sum('amount_spent');
    }

    //Check if the budget category is overspent
    public function getIsOverspentAttribute() : bool
    {
        return $this->total_spent > $this->allocated_amount;
    }

    public function getRemainingBudgetAttribute() : float
    {
        return max(0, $this->allocated_amount - $this->total_spent);
    }
}
