<?php

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('lets a couple manage notifications through the api', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_COUPLE,
    ]);

    $notification = UserNotification::query()->create([
        'user_id' => $user->id,
        'title' => 'Booking Approved',
        'message' => 'Your venue booking has been approved.',
        'is_read' => false,
    ]);

    Sanctum::actingAs($user);

    getJson('/api/v1/couple/notifications')
        ->assertOk()
        ->assertJsonPath('data.0.id', $notification->id)
        ->assertJsonPath('data.0.title', 'Booking Approved')
        ->assertJsonPath('data.0.is_read', false);

    putJson('/api/v1/couple/notifications/'.$notification->id.'/read')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Notification marked as read.');

    expect($notification->refresh()->is_read)->toBeTrue();

    deleteJson('/api/v1/couple/notifications/'.$notification->id)
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Notification deleted successfully.');

    expect(UserNotification::query()->whereKey($notification->id)->exists())->toBeFalse();
});
