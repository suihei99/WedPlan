<?php

use App\Http\Controllers\web\Auth\WebAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    // Authentication routes
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);
    Route::get('/forgot-password', [WebAuthController::class, 'showForgotPasswordForm'])->name('password.request');

    // Registration routes
    Route::get('/register/couple', [WebAuthController::class, 'showRegisterCoupleForm'])->name('register.couple');
    Route::post('/register/couple', [WebAuthController::class, 'registerCouple']);

    Route::get('/register/vendor', [WebAuthController::class, 'showRegisterVendorForm'])->name('register.vendor');
    Route::post('/register/vendor', [WebAuthController::class, 'registerVendor']);
});

// Logout route
Route::post('/logout', [WebAuthController::class, 'logout'])->middleware('auth')->name('logout');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Additional admin routes can be added here
});

// Vendor routes
Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    // Vendor dashboard
    Route::get('/dashboard', function () {
        return view('vendor.dashboard');
    })->name('vendor.dashboard');

    // Additional vendor routes can be added here
});

// Couple routes
Route::middleware(['auth', 'role:couple'])->prefix('couple')->name('couple.')->group(function () {
    // Couple dashboard
    Route::get('/dashboard', function () {
        return view('couple.dashboard');
    })->name('couple.dashboard');

    // Additional couple routes can be added here
});
