<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'budget_category_id',
        'expense_name',
        'amount',
        'expense_date',
    ];
}
