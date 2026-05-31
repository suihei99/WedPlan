<?php

use App\Models\Couple;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('returns invitation details for the guest qr api route', function () {
    $user = User::factory()->create();

    Couple::query()->create([
        'user_id' => $user->id,
        'partner_1_name' => 'Adam',
        'partner_2_name' => 'Bella',
        'wedding_date' => '2026-12-25',
        'wedding_time' => '18:30:00',
        'wedding_venue' => 'Grand Ballroom',
    ]);

    Guest::query()->create([
        'user_id' => $user->id,
        'name' => 'Charlie Guest',
        'phone' => '+60111222333',
        'pax_count' => 3,
        'rsvp_status' => Guest::RSVP_PENDING,
        'invite_code' => 'INV12345',
        'qr_code_string' => 'INVITE:INV12345',
    ]);

    getJson('/api/v1/guest/qr/INV12345')
        ->assertSuccessful()
        ->assertJsonPath('data.invite_code', 'INV12345')
        ->assertJsonPath('data.guest_name', 'Charlie Guest')
        ->assertJsonPath('data.couple.display_name', 'Adam & Bella')
        ->assertJsonPath('data.wedding.venue', 'Grand Ballroom')
        ->assertJsonPath('data.checkin_url', url('/guest/checkin/INV12345'))
        ->assertJsonPath(
            'data.qr_image_url',
            'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data='.rawurlencode(url('/guest/checkin/INV12345'))
        );
});
