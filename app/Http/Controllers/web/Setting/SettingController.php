<?php

namespace App\Http\Controllers\web\Setting;

use App\Http\Controllers\Controller;
use App\Models\Couple;
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

        $couple = $user->couple;

        return view('couple.settings.index', compact('user', 'couple'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

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
}
