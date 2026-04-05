<?php

use App\Http\Controllers\web\Auth\WebAuthController;
use App\Http\Controllers\web\Couple\CoupleDashboardController;
use App\Http\Controllers\web\Vendor\VendorDashboardController;
use App\Http\Controllers\web\Admin\AdminDashboardController;
use App\Http\Controllers\web\Couple\TaskController;
use App\Http\Controllers\web\Couple\GuestController;
use App\Http\Controllers\Web\Couple\BudgetController;
use App\Http\Controllers\web\Setting\SettingController;
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
    // Route::get('dashboard', [CoupleDashboardController::class, 'index'])->name('dashboard');

    // Budget management
    Route::get('/budget', [BudgetController::class, 'index'])->name('budget.index');
    Route::get('/budget/create', [BudgetController::class, 'displayAddCategoryForm'])->name('budget.create');
    Route::post('/budget/create/added', [BudgetController::class, 'store'])->name('budget.store');
    Route::get('/budget/{budgetCategory}', [BudgetController::class, 'show'])->name('budget.show');
    Route::put('/budget/{budgetCategory}', [BudgetController::class, 'update'])->name('budget.update');
    Route::delete('/budget/{budgetCategory}', [BudgetController::class, 'destroy'])->name('budget.destroy');
    Route::put('/budget/alert', [BudgetController::class, 'updateLimit'])->name('budget.limit');

    // Expense management into Budget categories can be added here
    Route::get('/budget/{budgetCategory}/expenses', [BudgetController::class, 'showExpenses'])->name('budget.expenses');
    Route::put('/budget/{budgetCategory}/expenses{expense}', [BudgetController::class, 'completedExpense'])->name('budget.expenses.update');
    Route::get('/budget/{budgetCategory}/expenses/create', [BudgetController::class, 'displayAddExpenseForm'])->name('budget.expenses.create');
    Route::post('/budget/{budgetCategory}/expenses/added', [BudgetController::class, 'addExpense'])->name('budget.expenses.add');
    Route::get('/budget/{budgetCategory}/expenses/{expense}', [BudgetController::class, 'showExpense'])->name('budget.expenses.show');
    Route::put('/budget/{budgetCategory}/expenses/{expense}', [BudgetController::class, 'updateExpense'])->name('budget.expenses.update');
    Route::put('/budget/{budgetCategory}/expenses/{expense}/due-date', [BudgetController::class, 'dueDateExpense'])->name('budget.expenses.due-date');
    Route::delete('/budget/{budgetCategory}/expenses/{expense}', [BudgetController::class, 'destroyExpense'])->name('budget.expenses.delete');

    //Guest management routes can be added here
    Route::get('/guests', [GuestController::class, 'showGuests'])->name('guests.index');
    Route::get('/guests/create', [GuestController::class, 'displayAddGuestForm'])->name('guests.create');
    Route::post('/guests/create/added', [GuestController::class, 'store'])->name('guests.store');
    Route::get('/guests/{guest}', [GuestController::class, 'show'])->name('guests.show');
    Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
    Route::put('/guests/{guest}/rsvp', [GuestController::class, 'updateRSVP'])->name('guests.rsvp');
    Route::post('/guests/{guest}/check-in', [GuestController::class, 'checkin'])->name('guests.checkin');
    Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');

    //Task management routes can be added here
    Route::get('/tasks', [TaskController::class, 'showTasks'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'displayAddTaskForm'])->name('tasks.create');
    Route::post('/tasks/create/added', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}/complete', [TaskController::class, 'markComplete'])->name('tasks.complete');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Ai Budget Estimation route
    // Route::get('/ai/budget-estimation', [AiBudgetController::class, 'budgetEstimation'])->name('ai.budget-estimation');
    // Route::post('/ai/budget-estimation', [AiBudgetController::class, 'generateEstimation'])->name('ai.budget-estimation.generate');

    // Setting : update profile, change password,  etc. can be added here
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password.update');
});
