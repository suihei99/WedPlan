<?php

namespace App\Http\Controllers\web\Setting;

use App\Http\Controllers\Controller;
use App\Models\Couple;
use App\Models\User;
use App\Models\Vendor;
use App\Services\UserNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct(private readonly UserNotificationService $userNotificationService) {}

    public function index()
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        if ($user->role === User::ROLE_ADMIN) {
            return view('admin.setting.index', compact('user'));
        }

        if ($user->role === User::ROLE_VENDOR) {
            $vendor = $user->vendor;

            return view('vendor.settings.index', compact('user', 'vendor'));
        }

        $couple = $user->couple;

        return view('couple.settings.index', compact('user', 'couple'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        if ($user->role === User::ROLE_VENDOR) {
            $validated = $request->validateWithBag('profileUpdate', [
                'business_type' => ['required', 'string', 'max:255'],
                'contact_number' => ['required', 'string', 'max:20'],
                'address' => ['required', 'string', 'max:255'],
                'profile_photo' => ['nullable', 'file', 'mimes:png,webp,jpeg,jpg,gif', 'max:2048'],
                'business_documents' => ['nullable', 'file', 'mimes:pdf,png,webp,jpeg,jpg,gif', 'max:2048'],
            ]);

            $vendor = $user->vendor;

            abort_unless($vendor instanceof Vendor, 403, 'Vendor profile not found.');

            $vendor->update([
                'business_type' => $validated['business_type'],
                'contact_number' => $validated['contact_number'],
                'address' => $validated['address'],
            ]);

            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = $this->storeProfilePhoto($request->file('profile_photo'), $user->profile_photo_path);

                User::query()->whereKey($user->id)->update([
                    'profile_photo_path' => $profilePhotoPath,
                ]);
            }

            if ($request->hasFile('business_documents')) {
                $documentPath = $this->storeBusinessDocument($request->file('business_documents'), $vendor->business_documents);

                $vendor->update([
                    'business_documents' => $documentPath,
                ]);

                $this->userNotificationService->notifyAdminsVendorDocumentationUpdated($user, $vendor->fresh());
            }

            return back()->with('success', 'Vendor profile updated successfully.');
        }

        $validated = $request->validateWithBag('profileUpdate', [
            'partner_1_name' => ['required', 'string', 'max:255'],
            'partner_2_name' => ['required', 'string', 'max:255'],
            'wedding_date' => ['nullable', 'date'],
            'wedding_time' => ['nullable', 'date_format:H:i'],
            'wedding_venue' => ['nullable', 'string', 'max:255'],
            'total_budget_limit' => ['nullable', 'numeric', 'min:0'],
        ]);

        Couple::query()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return back()->with('success', 'Couple profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        $validated = $request->validateWithBag('passwordUpdate', [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'], 'passwordUpdate');
        }

        User::query()->whereKey($user->id)->update(['password' => $validated['password']]);

        return back()->with('success', 'Password updated successfully.');
    }

    private function storeProfilePhoto(UploadedFile $photo, ?string $existingPath = null): string
    {
        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        return $photo->store('profile-photos', 'public');
    }

    private function storeBusinessDocument(UploadedFile $document, ?string $existingPath = null): string
    {
        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        return $document->store('vendor-documents', 'public');
    }
}
