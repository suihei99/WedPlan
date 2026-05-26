<?php

use App\Models\Couple;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('renders the versioned ai budget beta notice modal on the web page', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_COUPLE,
    ]);

    Couple::query()->create([
        'user_id' => $user->id,
        'partner_1_name' => 'Alya',
        'partner_2_name' => 'Ben',
    ]);

    actingAs($user);

    $response = get(route('couple.ai.budget-estimation'));

    $response->assertSuccessful()
        ->assertSee(config('ai_budget.notice.title'))
        ->assertSee(config('ai_budget.notice.description'))
        ->assertSee('Version '.config('ai_budget.notice.version').' Beta')
        ->assertSee(config('ai_budget.notice.button_label'))
        ->assertSee(config('ai_budget.actions.print_label'))
        ->assertSee(config('ai_budget.actions.forget_label'));
});
