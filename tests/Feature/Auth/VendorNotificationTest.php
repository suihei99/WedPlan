<?php

use App\Mail\UserAlertMail;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

it('sends a pending-approval email when a vendor registers', function () {
    Mail::fake();
    Storage::fake('public');

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'email' => 'admin@example.com',
    ]);

    post(route('register.vendor'), [
        'email' => 'vendor@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'business_name' => 'Wed Studio',
        'business_type' => 'Photography',
        'contact_number' => '+60123456789',
        'address' => 'Kuala Lumpur',
        'business_documents' => UploadedFile::fake()->create('license.pdf', 200, 'application/pdf'),
    ])
        ->assertRedirect(route('login'));

    $vendorUser = User::query()->where('email', 'vendor@example.com')->firstOrFail();

    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) use ($vendorUser) {
        return $mail->hasTo($vendorUser->email)
            && $mail->title === 'Vendor Registration Received';
    });

    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) use ($admin) {
        return $mail->hasTo($admin->email)
            && $mail->title === 'New Vendor Registration';
    });
});

it('sends an approval email when admin approves vendor account', function () {
    Mail::fake();

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $vendorUser = User::factory()->vendor()->create([
        'is_active' => false,
    ]);

    $vendor = Vendor::query()->create([
        'user_id' => $vendorUser->id,
        'business_name' => 'Vendor Biz',
        'business_type' => 'Catering',
        'contact_number' => '+60123456789',
        'status' => Vendor::STATUS_PENDING,
        'address' => 'Selangor',
        'business_documents' => 'vendor-documents/sample.pdf',
    ]);

    actingAs($admin);

    put(route('admin.vendors.approve', $vendor))
        ->assertRedirect();

    $vendor->refresh();
    $vendorUser->refresh();

    expect($vendor->status)->toBe(Vendor::STATUS_APPROVED);
    expect($vendorUser->is_active)->toBeTrue();

    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) use ($vendorUser) {
        return $mail->hasTo($vendorUser->email)
            && $mail->title === 'Vendor Account Approved';
    });
});
