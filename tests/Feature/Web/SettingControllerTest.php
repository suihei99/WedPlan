<?php

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class)->group('feature');

it('rejects invalid malaysia contact numbers on the vendor settings page', function () {
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

    $response = $this->actingAs($user)
        ->from(route('vendor.settings.index'))
        ->put(route('vendor.settings.profile.update'), [
            'business_type' => 'photography',
            'contact_number' => '12345',
            'address' => 'Kuala Lumpur',
        ]);

    $response->assertRedirect(route('vendor.settings.index'));
    $response->assertSessionHasErrorsIn('profileUpdate', ['contact_number']);
});

it('rejects business documents that are not pdf or png on the vendor settings page', function () {
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

    $response = $this->actingAs($user)
        ->from(route('vendor.settings.index'))
        ->put(route('vendor.settings.profile.update'), [
            'business_type' => 'photography',
            'contact_number' => '+60123456789',
            'address' => 'Kuala Lumpur',
            'business_documents' => UploadedFile::fake()->create('license.txt', 10, 'text/plain'),
        ]);

    $response->assertRedirect(route('vendor.settings.index'));
    $response->assertSessionHasErrorsIn('profileUpdate', ['business_documents']);
});
