<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Auth\ApiAuthController;
use App\Http\Controllers\Api\v1\Couple\ApiBudgetController;


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
        Route::middleware('role:couple')->prefix('couple')->group(function () {
        //    Route::get('dashboard', [CoupleDashboardController::class, 'index']);\

            // Budget management API routes
            Route::get('/budget', [ApiBudgetController::class, 'index']);
            Route::post('/budget', [ApiBudgetController::class, 'store']);
            Route::get('/budget/{budgetCategory}', [ApiBudgetController::class, 'show']);
            Route::put('/budget/{budgetCategory}', [ApiBudgetController::class, 'update']);
            Route::delete('/budget/{budgetCategory}', [ApiBudgetController::class, 'destroy']);

            // Expenses with Budget Categories API routes can be added here in the future
            
            // Guest management API routes can be added here in the future

            // Task management API routes can be added here in the future

            // AI Budgeting API routes can be added here in the future

            //Setting - Couple
            Route::get('/settings', [ApiSettingController::class, 'index']);
            Route::put('/settings', [ApiSettingController::class, 'update']);
        });
    });
});

