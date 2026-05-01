<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(private readonly UserNotificationService $userNotificationService) {}

    /**
     * Login - Authenticate user and generate token (vendor, couple, admin)
     * Return user data and token on success, error message on failure
     */
    public function login(array $credentials): ?array
    {
        $user = User::where('email', $credentials['email'])->where('is_active', true)->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return null; // Invalid credentials or inactive account
        }

        if ($user->isVendor()) {
            $vendor = $user->vendor;

            if (! $vendor || $vendor->status !== Vendor::STATUS_APPROVED) {
                return null;
            }
        }

        // Eager load related data based on role
        if ($user->isCouple()) {
            $user->load('couple'); // Load couple profile
        } elseif ($user->isVendor()) {
            $user->load('vendor'); // Load vendor profile
        }

        // Generate token for authenticated user
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'role' => $user->role, // Return the role of the user (vendor, couple, admin)
        ];
    }

    /*
    * Register a Couple.
    * Create a new user with role 'couple' and associated couple profile.
    */
    public function registerCouple(array $data): ?User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => User::ROLE_COUPLE,
            ]);

            $user->couple()->create([
                'partner_1_name' => $data['partner_1_name'], // Required field for couple profile
                'partner_2_name' => $data['partner_2_name'], // Required field for couple profile
                'wedding_date' => $data['wedding_date'] ?? null,
                'wedding_time' => $data['wedding_time'] ?? null,
                'wedding_venue' => $data['wedding_venue'] ?? null,
                'total_budget_limit' => $data['total_budget_limit'] ?? null,
            ]);

            $this->userNotificationService->notifyRegistrationSuccess($user);

            return $user->load('couple'); // Return user with couple profile
        });
    }

    /*
    * Register a Vendor.
    * Create a new user with role 'vendor' and associated vendor profile.
    */
    public function registerVendor(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $businessDocumentPath = null;

            if (($data['business_documents'] ?? null) instanceof UploadedFile) {
                $businessDocumentPath = $data['business_documents']->store('vendor-documents', 'public');
            }

            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => User::ROLE_VENDOR,
            ]);

            $user->vendor()->create([
                'business_name' => $data['business_name'],
                'business_type' => $data['business_type'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => $data['status'] ?? 'pending', // Default to 'pending' if not provided, admin can later approve or reject the vendor
                'business_documents' => $businessDocumentPath,
            ]);

            $this->userNotificationService->notifyVendorPendingApproval($user);

            return $user->load('vendor'); // Return user with vendor profile
        });
    }
}
