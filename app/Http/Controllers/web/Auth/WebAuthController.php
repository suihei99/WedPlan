<?php

namespace App\Http\Controllers\web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Auth\LoginRequest;
use App\Http\Requests\Requests\Auth\RegisterCoupleRequest;
use App\Http\Requests\Requests\Auth\RegisterVendorRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class WebAuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    // Show login form & registration forms (vendor & couple)
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterCoupleForm()
    {
        return view('auth.register-couple');
    }

    public function showRegisterVendorForm()
    {
        return view('auth.register-vendor');
    }

    // Show forgot password form
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Handle login & registration
    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (! $result) {
            return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
        }

        Auth::login($result['user']);
        $request->session()->regenerate();

        // redirect based on role
        return match ($result['user']->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_COUPLE => redirect()->route('couple.dashboard'),
            User::ROLE_VENDOR => redirect()->route('vendor.dashboard'),
            default => redirect('/')
        };

    }

    // Handle Registration for Couples
    public function registerCouple(RegisterCoupleRequest $request)
    {
        $user = $this->authService->registerCouple($request->validated());
        Auth::login($user);

        return redirect()->route('couple.dashboard')->with('success', 'Registration successful! Welcome to our wedding planning platform.');
    }

    // Handle Registration for Vendors
    public function registerVendor(RegisterVendorRequest $request)
    {
        $user = $this->authService->registerVendor($request->validated());
        Auth::login($user);

        return redirect()->route('login')->with('info', 'Registration successful! Your account is pending approval. We will notify you once it has been reviewed.');
    }

    // Handle Logout
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
