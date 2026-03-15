<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\Auth\WebAuthController;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
    Route::get('/register/couple', [WebAuthController::class, 'showRegisterCoupleForm'])->name('register.couple');
    Route::get('/register/vendor', [WebAuthController::class, 'showRegisterVendorForm'])->name('register.vendor');
});


