<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Login - Authenticate user and generate token (vendor, couple, admin)
     * Return user data and token on success, error message on failure
     *
     * @param  string  $email
     * @param  string  $password
     */
    public function Login(array $credentials): ?array
    {
        $user = User::where('email', $credentials['email'])->where('is_active', true)->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return null; // Invalid credentials or inactive account
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
                'partner1_name' => $data['partner1_name'], // Required field for couple profile
                'partner2_name' => $data['partner2_name'], // Required field for couple profile
                'wedding_date' => $data['wedding_date'] ?? null,
                'wedding_time' => $data['wedding_time'] ?? null,
                'wedding_location' => $data['wedding_location'] ?? null,
                'total_budget_limit' => $data['total_budget_limit'] ?? null,
            ]);

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
                'business_documents' => $data['business_documents'] ?? null,
            ]);

            return $user->load('vendor'); // Return user with vendor profile
        });
    }
}
