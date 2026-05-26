<?php

use App\Mail\UserAlertMail;
use App\Models\Booking;
use App\Models\Couple;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Contract\Messaging;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\mock;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('sends email, web, and push notifications when a vendor creates a booking from the web controller', function () {
    Mail::fake();
    mock(Messaging::class, function ($mock) {
        $mock->shouldReceive('send')->once()->andReturn('message-id');
    });

    $vendor = User::factory()->vendor()->create();
    $couple = User::factory()->create([
        'device_token' => 'couple-device-web',
    ]);

    Couple::query()->create([
        'user_id' => $couple->id,
        'partner_1_name' => 'Alya',
        'partner_2_name' => 'Ben',
        'total_budget_limit' => 1000,
    ]);

    actingAs($vendor);

    $response = post(route('vendor.booking.store'), [
        'couple_id' => $couple->id,
        'type_service' => 'Photography',
        'booking_date' => '2026-12-25',
        'status' => true,
        'notes' => 'Engagement shoot booking',
    ]);

    $response->assertRedirect(route('vendor.booking.index'));

    $booking = Booking::query()->firstOrFail();

    expect($booking->user_id)->toBe($vendor->id);
    expect($booking->couple_id)->toBe($couple->id);
    expect(UserNotification::query()->where('user_id', $couple->id)->value('title'))->toBe('Booking Created');

    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) use ($couple) {
        return $mail->hasTo($couple->email)
            && $mail->title === 'Booking Created';
    });
});

it('sends email, web, and push notifications when a vendor creates a booking from the api controller', function () {
    Mail::fake();
    mock(Messaging::class, function ($mock) {
        $mock->shouldReceive('send')->once()->andReturn('message-id');
    });

    $vendor = User::factory()->vendor()->create();
    $couple = User::factory()->create([
        'device_token' => 'couple-device-api',
    ]);

    Couple::query()->create([
        'user_id' => $couple->id,
        'partner_1_name' => 'Alya',
        'partner_2_name' => 'Ben',
        'total_budget_limit' => 1000,
    ]);

    Sanctum::actingAs($vendor);

    postJson('/api/v1/vendor/bookings', [
        'couple_id' => $couple->id,
        'type_service' => 'Catering',
        'booking_date' => '2026-12-31',
        'status' => true,
        'notes' => 'Dinner service booking',
    ])
        ->assertCreated()
        ->assertJsonPath('message', 'Booking created successfully.')
        ->assertJsonPath('data.type_service', 'Catering');

    $booking = Booking::query()->firstOrFail();

    expect($booking->user_id)->toBe($vendor->id);
    expect($booking->couple_id)->toBe($couple->id);
    expect(UserNotification::query()->where('user_id', $couple->id)->value('title'))->toBe('Booking Created');

    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) use ($couple) {
        return $mail->hasTo($couple->email)
            && $mail->title === 'Booking Created';
    });
});
