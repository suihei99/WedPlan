<?php

use App\Http\Controllers\Api\v1\Auth\ApiAuthController;
use App\Http\Controllers\Api\v1\Couple\ApiAiBudgetController;
use App\Http\Controllers\Api\v1\Couple\ApiBudgetController;
use App\Http\Controllers\Api\v1\Couple\ApiDashboardController;
use App\Http\Controllers\Api\v1\Couple\ApiExpenseController;
use App\Http\Controllers\Api\v1\Couple\ApiGuestController;
use App\Http\Controllers\Api\v1\Couple\ApiTaskController;
use App\Http\Controllers\Api\v1\Setting\ApiSettingController;
use App\Http\Controllers\Api\v1\Vendor\ApiBookingController;
use App\Http\Controllers\Api\v1\Vendor\ApiDashboardController as VendorApiDashboardController;
use App\Http\Controllers\Api\v1\Vendor\ApiNotificationController;
use App\Http\Controllers\Api\v1\Vendor\ApiServiceController;
use Illuminate\Support\Facades\Route;

/*
* API Routes - These Routes are API restful for integration with Flutter app which used for vendor, couple and guest (from couple).
* All routes are prefixed with /api/v1/ for versioning and future scalability.
* Admin is no longer included in the API routes as they will manage the system through the web interface.
*/

// Public routes
Route::prefix('v1')->group(function () {
    Route::get('/guest/qr/{code}', [ApiGuestController::class, 'qr']);
    Route::get('/guest/invitation/{code}', [ApiGuestController::class, 'invitation']);

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

        // Setting - all authenticated users
        Route::get('/settings', [ApiSettingController::class, 'index']);
        Route::put('/settings', [ApiSettingController::class, 'update']);

        // Vendor
        Route::middleware('role:vendor')->prefix('vendor')->group(function () {
            Route::get('/dashboard', [VendorApiDashboardController::class, 'index']);
            Route::get('/services', [ApiServiceController::class, 'index']);
            Route::post('/services', [ApiServiceController::class, 'store']);
            Route::get('/services/{service}', [ApiServiceController::class, 'show']);
            Route::put('/services/{service}', [ApiServiceController::class, 'update']);
            Route::delete('/services/{service}', [ApiServiceController::class, 'destroy']);
            Route::apiResource('bookings', ApiBookingController::class);
            Route::get('/notifications', [ApiNotificationController::class, 'index']);
            Route::get('/notifications/{notification}', [ApiNotificationController::class, 'show']);
            Route::put('/notifications/{notification}/read', [ApiNotificationController::class, 'markAsRead']);
            Route::delete('/notifications/{notification}', [ApiNotificationController::class, 'destroy']);
        });

        // // Couple
        Route::middleware('role:couple')->prefix('couple')->group(function () {
            // Dashboard
            Route::get('/dashboard', [ApiDashboardController::class, 'index']);

            // Budget management API routes
            Route::get('/budget', [ApiBudgetController::class, 'index']);
            Route::post('/budget', [ApiBudgetController::class, 'store']);
            Route::get('/budget/{budgetCategory}', [ApiBudgetController::class, 'show']);
            Route::put('/budget/{budgetCategory}', [ApiBudgetController::class, 'update']);
            Route::delete('/budget/{budgetCategory}', [ApiBudgetController::class, 'destroy']);

            // Expense API routes
            Route::get('/expenses', [ApiExpenseController::class, 'index']);
            Route::post('/expenses', [ApiExpenseController::class, 'store']);
            Route::get('/expenses/{expense}', [ApiExpenseController::class, 'show']);
            Route::put('/expenses/{expense}', [ApiExpenseController::class, 'update']);
            Route::delete('/expenses/{expense}', [ApiExpenseController::class, 'destroy']);

            // Guest API routes
            Route::get('/guests', [ApiGuestController::class, 'index']);
            Route::post('/guests', [ApiGuestController::class, 'store']);
            Route::get('/guests/{guest}', [ApiGuestController::class, 'show']);
            Route::put('/guests/{guest}', [ApiGuestController::class, 'update']);
            Route::put('/guests/{guest}/rsvp', [ApiGuestController::class, 'updateRsvp']);
            Route::post('/guests/{guest}/check-in', [ApiGuestController::class, 'checkin']);
            Route::delete('/guests/{guest}', [ApiGuestController::class, 'destroy']);

            // Task API routes
            Route::get('/tasks', [ApiTaskController::class, 'index']);
            Route::post('/tasks', [ApiTaskController::class, 'store']);
            Route::get('/tasks/{task}', [ApiTaskController::class, 'show']);
            Route::put('/tasks/{task}', [ApiTaskController::class, 'update']);
            Route::put('/tasks/{task}/complete', [ApiTaskController::class, 'markComplete']);
            Route::delete('/tasks/{task}', [ApiTaskController::class, 'destroy']);

            // AI Budgeting API routes
            Route::post('/ai-budget/estimate', [ApiAiBudgetController::class, 'estimateInitial']);
            Route::post('/ai-budget/chat', [ApiAiBudgetController::class, 'chat']);

            // Setting - Couple
            Route::get('/settings', [ApiSettingController::class, 'index']);
            Route::put('/settings', [ApiSettingController::class, 'update']);
        });
    });
});
