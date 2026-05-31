<?php

use App\Models\BudgetCategory;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('accepts lowercase payment_method when creating an expense through the api', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_COUPLE,
    ]);

    Sanctum::actingAs($user);

    $category = BudgetCategory::query()->create([
        'user_id' => $user->id,
        'category_name' => 'Venue',
        'allocated_amount' => 5000,
    ]);

    postJson('/api/v1/couple/expenses', [
        'budget_category_id' => $category->id,
        'expense_name' => 'Hall deposit',
        'amount' => 1200,
        'date_paid' => '2026-06-01',
        'description' => 'Initial payment',
        'payment_method' => 'cash',
    ])
        ->assertCreated()
        ->assertJsonPath('data.payment_method', Expense::METHOD_CASH);

    expect(Expense::query()->first()?->payment_method)->toBe(Expense::METHOD_CASH);
});

it('accepts lowercase payment_method when updating an expense through the api', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_COUPLE,
    ]);

    Sanctum::actingAs($user);

    $category = BudgetCategory::query()->create([
        'user_id' => $user->id,
        'category_name' => 'Catering',
        'allocated_amount' => 8000,
    ]);

    $expense = Expense::query()->create([
        'budget_category_id' => $category->id,
        'expense_name' => 'Food tasting',
        'amount' => 200,
        'date_paid' => '2026-06-02',
        'description' => 'Tasting session',
        'payment_method' => Expense::METHOD_CASH,
    ]);

    putJson('/api/v1/couple/expenses/'.$expense->id, [
        'budget_category_id' => $category->id,
        'expense_name' => 'Food tasting updated',
        'amount' => 300,
        'date_paid' => '2026-06-03',
        'description' => 'Updated tasting session',
        'payment_method' => 'bank_transfer',
    ])
        ->assertOk()
        ->assertJsonPath('data.payment_method', Expense::METHOD_TRANSFER);

    expect($expense->refresh()->payment_method)->toBe(Expense::METHOD_TRANSFER);
});
