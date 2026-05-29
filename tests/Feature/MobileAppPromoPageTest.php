<?php

use App\Models\Couple;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ViewErrorBag;

uses(RefreshDatabase::class);

it('shows the mobile app promo section on the welcome page', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Mobile App', false);
    $response->assertSee(asset('downloads/wedplan-release.apk'), false);
    $response->assertSee('Download APK', false);
});

it('uses the apk download link in guest whatsapp invitations', function () {
    $user = User::factory()->create();

    $couple = Couple::query()->create([
        'user_id' => $user->id,
        'partner_1_name' => 'Alya',
        'partner_2_name' => 'Ben',
        'total_budget_limit' => 1000,
    ]);

    $guest = Guest::query()->create([
        'user_id' => $user->id,
        'name' => 'Siti Guest',
        'phone' => '+60111222333',
        'pax_count' => 2,
        'rsvp_status' => Guest::RSVP_PENDING,
        'invite_code' => 'INVITE123',
        'qr_code_string' => 'INVITE:INVITE123',
    ]);

    $this->actingAs($user);

    $html = view('couple.guests.show', [
        'guest' => $guest,
        'couple' => $couple,
        'errors' => new ViewErrorBag,
    ])->render();

    expect(urldecode($html))->toContain(asset('downloads/wedplan-release.apk'));
    expect(urldecode($html))->toContain('Download the mobile app here');
});
