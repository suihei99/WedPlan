<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Auth\ApiAuthController;


/*
* API Routes - These Routes are API restful for integration with Flutter app which used for vendor, couple and guest (from couple). 
* All routes are prefixed with /api/v1/ for versioning and future scalability.
* Admin is no longer included in the API routes as they will manage the system through the web interface.
*/ 

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::prefix('auth')->group(function () {
        // Registration routes
        Route::post('/register/couple', [ApiAuthController::class, 'registerCouple']);
        Route::post('/register/vendor', [ApiAuthController::class, 'registerVendor']);

        // Login route
        Route::post('/login', [ApiAuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        // Logout route
        Route::post('auth/logout', [ApiAuthController::class, 'logout']);

        // // Vendor
        // Route::middleware('role:vendor')->prefix('vendor')->group(function () {
        //     Route::get('profile', [VendorApiController::class, 'profile']);
        //     Route::put('profile', [VendorApiController::class, 'updateProfile']);
        // });

        // // Couple
        // Route::middleware('role:couple')->prefix('couple')->group(function () {
        //     Route::get('profile', [CoupleApiController::class, 'profile']);
        //     Route::put('profile', [CoupleApiController::class, 'updateProfile']);
        // });
    });
});

