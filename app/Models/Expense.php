<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mockery\Generator\Method;


class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'budget_category_id',
        'expense_name',
        'amount',
        'expense_date',
        'description',
        'payment_method',
    ];

    protected function casts()
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
            'description' => 'string',
            'payment_method' => 'string',
        ];
    }

    // Define the relationship with the BudgetCategory model
    public function budgetCategory()
    {
        return $this->belongsTo(BudgetCategory::class, 'budget_category_id');
    }

    //Payment method contants
    const METHOD_CASH = 'Cash';
    const METHOD_CREDITCARD= 'Credit Card';
    const METHOD_DEDITCARD = 'Debit Card';
    const METHOD_TRANSFER = 'Bank_Transfer';

    const METHOD = [self::METHOD_CASH, self::METHOD_CREDITCARD, self::METHOD_DEDITCARD, self::METHOD_TRANSFER];
}
