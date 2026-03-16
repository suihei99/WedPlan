<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Auth\LoginRequest;
use App\Http\Requests\Requests\Auth\RegisterCoupleRequest;
use App\Http\Requests\Requests\Auth\RegisterVendorRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    /**
     * v1 API Authentication Controller
     * @param AuthService $authService
     */
    public function __construct(private readonly AuthService $authService){}

    // Handle login & registration
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if(! $result){
            return response()->json(['message' => 'Invalid credentials'],  401);
        }

        return response()->json([
            'message' => 'Login successful',
            'role' => $result['role'],
            'token' => $result['token'],
            'user' => $result['user']], 
            200);
    }

    // Handle Registration for Couples
    public function registerCouple(RegisterCoupleRequest $request): JsonResponse
    {
        $user = $this->authService->registerCouple($request->validated());

        return response()->json([
            'message' => 'Registration successful',
            'role'=> $user->role,
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user
        ], 201);
    }

    // Handle Registration for Vendors
    public function registerVendor(RegisterVendorRequest $request) : JsonResponse
    {
        $user = $this->authService->registerVendor($request->validated());

        return response()->json([
            'message' => 'Registration successful. Pending admin approval.',
            'user' => $user
        ], 201);
    }

    // Handle Logout
    public function logout(Request $request) : JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}

