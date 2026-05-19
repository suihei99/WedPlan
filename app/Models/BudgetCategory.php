<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetCategory extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $table = 'budgetcategories';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'category_name',
        'allocated_amount',
    ];

    protected function casts(): array
    {
        return [
            'allocated_amount' => 'decimal:2',
        ];
    }

    // Define the relationship with the User model
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship with the Expense model
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // Accessor to calculate the total amount spent in this budget category
    public function getTotalSpentAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    // Check if the budget category is overspent
    public function getIsOverspentAttribute(): bool
    {
        return $this->total_spent > $this->allocated_amount;
    }

    public function getRemainingBudgetAttribute(): float
    {
        return max(0, $this->allocated_amount - $this->total_spent);
    }

    // Amount overspent for this category (0 if not overspent)
    public function getOverspentAmountAttribute(): float
    {
        $overspent = $this->total_spent - $this->allocated_amount;

        return $overspent > 0 ? round($overspent, 2) : 0.0;
    }
}
