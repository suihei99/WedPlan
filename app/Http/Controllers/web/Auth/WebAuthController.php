<?php

namespace App\Http\Controllers\web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Requests\Auth\LoginRequest;
use App\Http\Requests\Requests\Auth\RegisterCoupleRequest;
use App\Http\Requests\Requests\Auth\RegisterVendorRequest;
use App\Http\Requests\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

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

    public function showResetPasswordForm(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            return redirect()->route('password.request')
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        return redirect()->route('password.request')->with('status', __($status));
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        return redirect()->route('login')->with('status', __($status));
    }

    // Handle login & registration
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $result = $this->authService->login($request->validated());

        if (! $result) {
            $pendingVendor = User::query()
                ->where('email', $validated['email'])
                ->where('role', User::ROLE_VENDOR)
                ->whereHas('vendor', fn ($query) => $query->where('status', '!=', Vendor::STATUS_APPROVED))
                ->exists();

            if ($pendingVendor) {
                return back()->withErrors(['email' => 'Your vendor account is pending admin approval.'])->onlyInput('email');
            }

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
        $this->authService->registerVendor($request->validated());

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
