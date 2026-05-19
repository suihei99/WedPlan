<?php

namespace App\Http\Controllers\Api\v1\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ApiSettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => new UserResource($user->loadMissing(['couple', 'vendor'])),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'device_token' => ['sometimes', 'nullable', 'string', 'max:255'],
            'profile_photo' => ['sometimes', 'nullable', 'file', 'mimes:png,webp,jpeg,jpg,gif', 'max:2048'],
            'current_password' => ['required_with:password', 'string'],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
        ]);

        if (isset($validated['password'])) {
            if (! isset($validated['current_password']) || ! Hash::check($validated['current_password'], $user->password)) {
                return response()->json(['message' => 'Current password is incorrect.'], 422);
            }

            $user->password = $validated['password'];
        }

        foreach (['email', 'device_token'] as $field) {
            if (array_key_exists($field, $validated)) {
                $user->{$field} = $validated[$field];
            }
        }

        if ($request->hasFile('profile_photo')) {
            abort_unless($user->role === User::ROLE_VENDOR, 403, 'Profile photo upload is only available for vendors.');

            $profilePhoto = $request->file('profile_photo');

            if ($profilePhoto instanceof UploadedFile) {
                $this->storeProfilePhoto($user, $profilePhoto);
            }
        }

        $user->save();

        return response()->json([
            'message' => 'Settings updated successfully.',
            'data' => new UserResource($user->loadMissing(['couple', 'vendor'])),
        ]);
    }

    private function storeProfilePhoto(User $user, UploadedFile $photo): void
    {
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->profile_photo_path = $photo->store('profile-photos', 'public');
    }
}
