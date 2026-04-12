<?php

namespace App\Http\Controllers\web\Setting;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return view('couple.settings.index', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'device_token' => ['nullable', 'string', 'max:255'],
            'profile_photo_path' => ['nullable', 'string'],
        ]);

        User::query()->whereKey($user->id)->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        User::query()->whereKey($user->id)->update(['password' => $validated['password']]);

        return back()->with('success', 'Password updated successfully.');
    }
}
