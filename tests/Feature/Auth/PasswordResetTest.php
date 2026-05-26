<?php

use App\Models\User;
use App\Notifications\BrandedResetPasswordNotification;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('sends a password reset link email', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'reset@example.com',
    ]);

    post(route('password.email'), [
        'email' => $user->email,
    ])
        ->assertRedirect(route('password.request'))
        ->assertSessionHas('status');

    Notification::assertSentTo($user, BrandedResetPasswordNotification::class);
});

it('sends a password reset link email for vendor accounts', function () {
    Notification::fake();

    $user = User::factory()->vendor()->create([
        'email' => 'vendor-reset@example.com',
    ]);

    post(route('password.email'), [
        'email' => $user->email,
    ])
        ->assertRedirect(route('password.request'))
        ->assertSessionHas('status');

    Notification::assertSentTo($user, BrandedResetPasswordNotification::class);
});

it('resets the password with a valid token', function () {
    $user = User::factory()->create([
        'password' => 'OldPassword123',
    ]);

    /** @var PasswordBroker $broker */
    $broker = Password::broker();
    $token = $broker->createToken($user);

    post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPassword123',
        'password_confirmation' => 'NewPassword123',
    ])
        ->assertRedirect(route('login'))
        ->assertSessionHas('status');

    $user->refresh();

    expect(Hash::check('NewPassword123', $user->password))->toBeTrue();
});
