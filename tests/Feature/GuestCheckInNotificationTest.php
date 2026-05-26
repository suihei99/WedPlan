<?php

use App\Mail\UserAlertMail;
use App\Models\Couple;
use App\Models\Guest;
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

it('sends email, web, and push notifications when a couple checks in a guest from the web controller', function () {
    Mail::fake();
    mock(Messaging::class, function ($mock) {
        $mock->shouldReceive('send')->once()->andReturn('message-id');
    });

    $user = User::factory()->create([
        'device_token' => 'device-token-web',
    ]);

    Couple::query()->create([
        'user_id' => $user->id,
        'partner_1_name' => 'Alya',
        'partner_2_name' => 'Ben',
        'total_budget_limit' => 1000,
    ]);

    $guest = Guest::query()->create([
        'user_id' => $user->id,
        'name' => 'Charlie Guest',
        'phone' => '+60111222333',
        'pax_count' => 2,
        'rsvp_status' => Guest::RSVP_PENDING,
        'invite_code' => 'CHKWEB01',
        'qr_code_string' => 'INVITE:CHKWEB01',
    ]);

    actingAs($user);

    post(route('couple.guests.checkin', $guest))
        ->assertRedirect(route('couple.guests.index'));

    $guest->refresh();

    expect($guest->rsvp_status)->toBe(Guest::RSVP_CONFIRMED);
    expect(UserNotification::query()->where('user_id', $user->id)->value('title'))->toBe('Guest Checked In: Charlie Guest');

    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) use ($user) {
        return $mail->hasTo($user->email)
            && $mail->title === 'Guest Checked In: Charlie Guest';
    });
});

it('sends email, web, and push notifications when a couple checks in a guest from the api controller', function () {
    Mail::fake();
    mock(Messaging::class, function ($mock) {
        $mock->shouldReceive('send')->once()->andReturn('message-id');
    });

    $user = User::factory()->create([
        'device_token' => 'device-token-api',
    ]);

    Couple::query()->create([
        'user_id' => $user->id,
        'partner_1_name' => 'Alya',
        'partner_2_name' => 'Ben',
        'total_budget_limit' => 1000,
    ]);

    $guest = Guest::query()->create([
        'user_id' => $user->id,
        'name' => 'Dana Guest',
        'phone' => '+60111222334',
        'pax_count' => 1,
        'rsvp_status' => Guest::RSVP_PENDING,
        'invite_code' => 'CHKAPI01',
        'qr_code_string' => 'INVITE:CHKAPI01',
    ]);

    Sanctum::actingAs($user);

    postJson('/api/v1/couple/guests/'.$guest->id.'/check-in')
        ->assertSuccessful()
        ->assertJsonPath('message', 'Guest checked in successfully.')
        ->assertJsonPath('data.rsvp_status', Guest::RSVP_CONFIRMED);

    $guest->refresh();

    expect($guest->rsvp_status)->toBe(Guest::RSVP_CONFIRMED);
    expect(UserNotification::query()->where('user_id', $user->id)->value('title'))->toBe('Guest Checked In: Dana Guest');

    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) use ($user) {
        return $mail->hasTo($user->email)
            && $mail->title === 'Guest Checked In: Dana Guest';
    });
});
