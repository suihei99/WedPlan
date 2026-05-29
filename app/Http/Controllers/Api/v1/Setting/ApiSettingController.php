<?php

namespace App\Http\Controllers\Api\v1\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Setting\UpdateApiSettingRequest;
use App\Http\Resources\UserResource;
use App\Models\Couple;
use App\Models\User;
use App\Models\Vendor;
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

    public function update(UpdateApiSettingRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('profile_photo') && ! $user->isVendor()) {
            abort(403, 'Profile photo upload is only available for vendors.');
        }

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

        if ($user->isVendor()) {
            $this->updateVendorProfile($user, $validated, $request);
        }

        if ($user->isCouple()) {
            $this->updateCoupleProfile($user, $validated);
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

    private function updateVendorProfile(User $user, array $validated, UpdateApiSettingRequest $request): void
    {
        $vendorData = array_intersect_key($validated, array_flip([
            'business_name',
            'business_type',
            'contact_number',
            'address',
        ]));

        if ($vendorData !== []) {
            $user->vendor()->updateOrCreate(
                ['user_id' => $user->id],
                $vendorData
            );
        }

        if ($request->hasFile('profile_photo')) {
            abort_unless($user->role === User::ROLE_VENDOR, 403, 'Profile photo upload is only available for vendors.');

            $profilePhoto = $request->file('profile_photo');

            if ($profilePhoto instanceof UploadedFile) {
                $this->storeProfilePhoto($user, $profilePhoto);
            }
        }

        if ($request->hasFile('business_documents')) {
            $vendor = $user->vendor;

            abort_unless($vendor instanceof Vendor, 403, 'Vendor profile not found.');

            $document = $request->file('business_documents');

            if ($document instanceof UploadedFile) {
                if ($vendor->business_documents) {
                    Storage::disk('public')->delete($vendor->business_documents);
                }

                $vendor->update([
                    'business_documents' => $document->store('vendor-documents', 'public'),
                ]);
            }
        }
    }

    private function updateCoupleProfile(User $user, array $validated): void
    {
        $coupleData = array_intersect_key($validated, array_flip([
            'partner_1_name',
            'partner_2_name',
            'wedding_date',
            'wedding_time',
            'wedding_venue',
            'total_budget_limit',
        ]));

        if ($coupleData === []) {
            return;
        }

        Couple::query()->updateOrCreate(
            ['user_id' => $user->id],
            $coupleData
        );
    }
}
