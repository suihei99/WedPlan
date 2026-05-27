<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('saves the device token when a user logs in through the api', function () {
    $user = User::factory()->create([
        'email' => 'login-device@example.com',
        'password' => 'Password123!',
        'device_token' => null,
    ]);

    postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'Password123!',
        'device_token' => 'firebase-token-123',
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Login successful')
        ->assertJsonPath('token', fn (string $token) => $token !== '');

    expect($user->refresh()->device_token)->toBe('firebase-token-123');
});
