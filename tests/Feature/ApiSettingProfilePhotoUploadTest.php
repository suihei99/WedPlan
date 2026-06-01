<?php

use App\Models\Couple;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class)->group('feature');

beforeEach(function () {
    Storage::fake('public');
});

it('stores a vendor profile photo on the public disk', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_VENDOR,
    ]);

    Vendor::query()->create([
        'user_id' => $user->id,
        'business_name' => 'Vendor Co',
        'business_type' => 'photography',
        'contact_number' => '+60123456789',
        'address' => 'Kuala Lumpur',
        'status' => Vendor::STATUS_PENDING,
    ]);

    Sanctum::actingAs($user);

    $request = HttpRequest::create('/api/v1/settings', 'POST', [
        '_method' => 'PUT',
    ], [], [
        'profile_photo' => UploadedFile::fake()->image('avatar.jpg'),
    ], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = app()->handle($request);

    expect($response->getStatusCode())->toBe(200);

    $payload = json_decode($response->getContent(), true);

    expect($payload['data']['role'])->toBe(User::ROLE_VENDOR);
    expect($payload['data']['profile_photo_path'])->toStartWith('profile-photos/');
    expect($payload['data']['profile_photo_url'])->toContain('/storage/profile-photos/');

    $user->refresh();

    expect($user->profile_photo_path)->not->toBeNull();
    expect(Storage::disk('public')->exists($user->profile_photo_path))->toBeTrue();
});

it('returns couple profile details in the settings payload', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_COUPLE,
    ]);

    Couple::query()->create([
        'user_id' => $user->id,
        'partner_1_name' => 'Alex',
        'partner_2_name' => 'Jordan',
        'wedding_date' => '2026-12-31',
        'wedding_venue' => 'KL Tower',
        'wedding_time' => '18:30',
        'total_budget_limit' => 50000,
    ]);

    Sanctum::actingAs($user);

    $request = HttpRequest::create('/api/v1/settings', 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = app()->handle($request);

    expect($response->getStatusCode())->toBe(200);

    $payload = json_decode($response->getContent(), true);

    expect($payload['data']['role'])->toBe(User::ROLE_COUPLE);
    expect($payload['data']['couple']['partner_1_name'])->toBe('Alex');
    expect($payload['data']['couple']['partner_2_name'])->toBe('Jordan');
});

it('updates couple profile details through the api settings endpoint', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_COUPLE,
    ]);

    Couple::query()->create([
        'user_id' => $user->id,
        'partner_1_name' => 'Alex',
        'partner_2_name' => 'Jordan',
    ]);

    Sanctum::actingAs($user);

    $request = HttpRequest::create('/api/v1/settings', 'POST', [
        '_method' => 'PUT',
        'partner_1_name' => 'Taylor',
        'partner_2_name' => 'Casey',
        'wedding_venue' => 'Putrajaya',
        'total_budget_limit' => '75000',
    ], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = app()->handle($request);

    expect($response->getStatusCode())->toBe(200);

    $user->refresh();

    expect($user->couple->partner_1_name)->toBe('Taylor');
    expect($user->couple->partner_2_name)->toBe('Casey');
    expect($user->couple->wedding_venue)->toBe('Putrajaya');
    expect((string) $user->couple->total_budget_limit)->toBe('75000.00');
});

it('updates vendor profile details through the api settings endpoint', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_VENDOR,
    ]);

    Vendor::query()->create([
        'user_id' => $user->id,
        'business_name' => 'Vendor Co',
        'business_type' => 'photography',
        'contact_number' => '+60123456789',
        'address' => 'Kuala Lumpur',
        'status' => Vendor::STATUS_PENDING,
    ]);

    Sanctum::actingAs($user);

    $request = HttpRequest::create('/api/v1/settings', 'POST', [
        '_method' => 'PUT',
        'business_name' => 'New Vendor Co',
        'business_type' => 'planner',
        'contact_number' => '+60111111111',
        'address' => 'Putrajaya',
    ], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = app()->handle($request);

    expect($response->getStatusCode())->toBe(200);

    $user->refresh();

    expect($user->vendor->business_name)->toBe('New Vendor Co');
    expect($user->vendor->business_type)->toBe('planner');
    expect($user->vendor->contact_number)->toBe('+60111111111');
    expect($user->vendor->address)->toBe('Putrajaya');
});

it('rejects invalid malaysia contact numbers in the api settings endpoint', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_VENDOR,
    ]);

    Vendor::query()->create([
        'user_id' => $user->id,
        'business_name' => 'Vendor Co',
        'business_type' => 'photography',
        'contact_number' => '+60123456789',
        'address' => 'Kuala Lumpur',
        'status' => Vendor::STATUS_PENDING,
    ]);

    Sanctum::actingAs($user);

    $request = HttpRequest::create('/api/v1/settings', 'POST', [
        '_method' => 'PUT',
        'business_name' => 'New Vendor Co',
        'business_type' => 'planner',
        'contact_number' => '12345',
        'address' => 'Putrajaya',
    ], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = app()->handle($request);

    expect($response->getStatusCode())->toBe(422);

    $payload = json_decode($response->getContent(), true);

    expect($payload['errors']['contact_number'][0])->toBe('Please enter a valid Malaysia number (e.g. +60123456789).');
});

it('rejects business documents that are not pdf or png in the api settings endpoint', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_VENDOR,
    ]);

    Vendor::query()->create([
        'user_id' => $user->id,
        'business_name' => 'Vendor Co',
        'business_type' => 'photography',
        'contact_number' => '+60123456789',
        'address' => 'Kuala Lumpur',
        'status' => Vendor::STATUS_PENDING,
    ]);

    Sanctum::actingAs($user);

    $request = HttpRequest::create('/api/v1/settings', 'POST', [
        '_method' => 'PUT',
        'business_name' => 'New Vendor Co',
        'business_type' => 'planner',
        'contact_number' => '+60111111111',
        'address' => 'Putrajaya',
    ], [], [
        'business_documents' => UploadedFile::fake()->create('license.txt', 10, 'text/plain'),
    ], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = app()->handle($request);

    expect($response->getStatusCode())->toBe(422);

    $payload = json_decode($response->getContent(), true);

    expect($payload['errors']['business_documents'][0])->toBe('Business document must be a PDF or PNG file.');
});

it('rejects profile photo uploads for non-vendor users', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_COUPLE,
    ]);

    Sanctum::actingAs($user);

    $request = HttpRequest::create('/api/v1/settings', 'POST', [
        '_method' => 'PUT',
    ], [], [
        'profile_photo' => UploadedFile::fake()->image('avatar.jpg'),
    ], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = app()->handle($request);

    expect($response->getStatusCode())->toBe(403);
    expect(json_decode($response->getContent(), true)['message'])->toBe('Profile photo upload is only available for vendors.');
});
