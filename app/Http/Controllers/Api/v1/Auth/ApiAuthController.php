<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Auth\LoginRequest;
use App\Http\Requests\Requests\Auth\RegisterCoupleRequest;
use App\Http\Requests\Requests\Auth\RegisterVendorRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    /**
     * v1 API Authentication Controller
     */
    public function __construct(private readonly AuthService $authService) {}

    // Handle login & registration
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->authService->login($validated);

        if (! $result) {
            $pendingVendor = User::query()
                ->where('email', $validated['email'])
                ->where('role', User::ROLE_VENDOR)
                ->whereHas('vendor', fn ($query) => $query->where('status', '!=', Vendor::STATUS_APPROVED))
                ->exists();

            if ($pendingVendor) {
                return response()->json(['message' => 'Vendor account is pending admin approval'], 403);
            }

            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'role' => $result['role'],
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
        ], 200);
    }

    public function registerCouple(RegisterCoupleRequest $request): JsonResponse
    {
        $user = $this->authService->registerCouple($request->validated());

        return response()->json([
            'message' => 'Registration successful',
            'role' => $user->role,
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => new UserResource($user),
        ], 201);
    }

    // Handle Registration for Vendors
    public function registerVendor(RegisterVendorRequest $request): JsonResponse
    {
        $user = $this->authService->registerVendor($request->validated());

        return response()->json([
            'message' => 'Registration successful. Pending admin approval.',
            'user' => new UserResource($user),
        ], 201);
    }

    // Handle Logout
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
