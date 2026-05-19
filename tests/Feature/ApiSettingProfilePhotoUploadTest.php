<?php

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
