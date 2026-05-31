<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('includes booking dates in the vendor list and detail api responses', function () {
    $couple = User::factory()->create();
    Sanctum::actingAs($couple);

    $vendorUser = User::factory()->vendor()->create();

    Vendor::query()->create([
        'user_id' => $vendorUser->id,
        'business_name' => 'Starlight Events',
        'business_type' => 'Wedding Planner',
        'contact_number' => '0123456789',
        'status' => Vendor::STATUS_APPROVED,
        'address' => 'Kuala Lumpur',
    ]);

    $service = Service::query()->create([
        'user_id' => $vendorUser->id,
        'service_name' => 'Premium Planning',
        'type_service' => 'Wedding Planner',
        'price_estimate' => 5000,
        'description' => 'Full wedding planning package',
    ]);

    Booking::query()->create([
        'user_id' => $vendorUser->id,
        'couple_id' => $couple->id,
        'type_service' => 'Wedding Planner',
        'booking_date' => '2026-12-25',
        'status' => true,
        'notes' => 'Christmas wedding planning session',
    ]);

    Booking::query()->create([
        'user_id' => $vendorUser->id,
        'couple_id' => $couple->id,
        'type_service' => 'Photography',
        'booking_date' => '2026-12-26',
        'status' => true,
        'notes' => 'Should not appear for the planner service',
    ]);

    getJson('/api/v1/couple/vendors')
        ->assertSuccessful()
        ->assertJsonPath('data.data.0.id', $service->id)
        ->assertJsonPath('data.data.0.booking_dates.0', '2026-12-25');

    getJson('/api/v1/couple/vendors/'.$service->id)
        ->assertSuccessful()
        ->assertJsonPath('data.booking_dates.0', '2026-12-25')
        ->assertJsonPath('data.service.booking_dates.0', '2026-12-25')
        ->assertJsonPath('data.vendor.business_name', 'Starlight Events');
});
