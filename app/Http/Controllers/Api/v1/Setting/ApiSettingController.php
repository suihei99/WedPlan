<?php

namespace App\Http\Controllers\Api\v1\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiSettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()->only(['id', 'email', 'role', 'device_token', 'profile_photo_path']),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,'.$request->user()->id],
            'device_token' => ['sometimes', 'nullable', 'string', 'max:255'],
            'profile_photo_path' => ['sometimes', 'nullable', 'string'],
            'current_password' => ['required_with:password', 'string'],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (isset($validated['password'])) {
            if (! isset($validated['current_password']) || ! Hash::check($validated['current_password'], $user->password)) {
                return response()->json(['message' => 'Current password is incorrect.'], 422);
            }

            $user->password = $validated['password'];
        }

        foreach (['email', 'device_token', 'profile_photo_path'] as $field) {
            if (array_key_exists($field, $validated)) {
                $user->{$field} = $validated[$field];
            }
        }

        $user->save();

        return response()->json([
            'message' => 'Settings updated successfully.',
            'data' => $user->only(['id', 'email', 'role', 'device_token', 'profile_photo_path']),
        ]);
    }
}
