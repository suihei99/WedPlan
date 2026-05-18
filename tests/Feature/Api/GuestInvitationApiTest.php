<?php

use App\Models\Couple;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('returns invitation details for a valid invite code', function () {
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

    $response = getJson('/api/v1/guest/invitation/INV12345');

    $response->assertSuccessful()
        ->assertJsonPath('data.invite_code', 'INV12345')
        ->assertJsonPath('data.guest_name', 'Charlie Guest')
        ->assertJsonPath('data.pax_count', 3)
        ->assertJsonPath('data.couple.partner_1_name', 'Adam')
        ->assertJsonPath('data.couple.partner_2_name', 'Bella')
        ->assertJsonPath('data.couple.display_name', 'Adam & Bella')
        ->assertJsonPath('data.wedding.venue', 'Grand Ballroom')
        ->assertJsonPath('data.wedding.date', '2026-12-25')
        ->assertJsonPath('data.wedding.time', '18:30')
        ->assertJsonPath('data.checkin_url', url('/guest/checkin/INV12345'));
});

it('returns not found for an unknown invite code', function () {
    $response = getJson('/api/v1/guest/invitation/UNKNOWN01');

    $response->assertNotFound()
        ->assertJsonPath('message', 'Invitation not found.');
});

it('allows guest to update rsvp status via public endpoint', function () {
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
        'name' => 'David Guest',
        'phone' => '+60111222333',
        'pax_count' => 2,
        'rsvp_status' => Guest::RSVP_PENDING,
        'invite_code' => 'RSV98765',
        'qr_code_string' => 'INVITE:RSV98765',
    ]);

    $response = $this->putJson('/api/v1/guest/rsvp/RSV98765', [
        'rsvp_status' => Guest::RSVP_CONFIRMED,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('message', 'RSVP updated successfully.')
        ->assertJsonPath('data.rsvp_status', 'confirmed');
});

it('returns not found when guest tries to rsvp with unknown code', function () {
    $response = $this->putJson('/api/v1/guest/rsvp/UNKNOWN99', [
        'rsvp_status' => Guest::RSVP_DECLINED,
    ]);

    $response->assertNotFound()
        ->assertJsonPath('message', 'Invitation not found.');
});

it('validates rsvp status on public endpoint', function () {
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
        'name' => 'Eve Guest',
        'phone' => '+60111222333',
        'pax_count' => 1,
        'rsvp_status' => Guest::RSVP_PENDING,
        'invite_code' => 'VAL45678',
        'qr_code_string' => 'INVITE:VAL45678',
    ]);

    $response = $this->putJson('/api/v1/guest/rsvp/VAL45678', [
        'rsvp_status' => 'invalid_status',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('rsvp_status');
});
