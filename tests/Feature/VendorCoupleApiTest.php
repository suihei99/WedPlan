<?php

use App\Models\Couple;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('returns couple information for vendor booking selection', function () {
    $vendor = User::factory()->vendor()->create();
    $couple = User::factory()->create([
        'email' => 'couple@example.com',
    ]);

    Couple::query()->create([
        'user_id' => $couple->id,
        'partner_1_name' => 'Alya',
        'partner_2_name' => 'Haziq',
        'total_budget_limit' => 1000,
    ]);

    Sanctum::actingAs($vendor);

    getJson('/api/v1/vendor/couples')
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $couple->id)
        ->assertJsonPath('data.0.email', 'couple@example.com')
        ->assertJsonPath('data.0.couple_name', 'Alya & Haziq')
        ->assertJsonPath('data.0.partner_1_name', 'Alya')
        ->assertJsonPath('data.0.partner_2_name', 'Haziq');
});
