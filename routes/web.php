<?php

use App\Http\Controllers\web\Admin\DashboardAdminController;
use App\Http\Controllers\web\Admin\ManageUserAdminController;
use App\Http\Controllers\web\Auth\WebAuthController;
use App\Http\Controllers\web\Couple\AiBudgetController;
use App\Http\Controllers\web\Couple\BudgetController;
use App\Http\Controllers\web\Couple\DashboardCoupleController;
use App\Http\Controllers\web\Couple\GuestController;
use App\Http\Controllers\web\Couple\TaskController;
use App\Http\Controllers\web\Couple\VendorListController;
use App\Http\Controllers\web\Notification\NotificationController;
use App\Http\Controllers\web\Setting\SettingController;
use App\Http\Controllers\web\Vendor\BookingVendorController;
use App\Http\Controllers\web\Vendor\DashboardVendorController;
use App\Http\Controllers\web\Vendor\ServiceVendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/guest/qr/{code}', [GuestController::class, 'qr'])->name('guest.qr');
// Route::get('/guest/checkin/{code}', [GuestController::class, 'publicCheckin'])->name('guest.checkin');

Route::middleware('guest')->group(function () {
    // Authentication routes
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);
    Route::get('/forgot-password', [WebAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [WebAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [WebAuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [WebAuthController::class, 'resetPassword'])->name('password.update');

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
    Route::get('/dashboard', [DashboardAdminController::class, 'showDashboard'])->name('dashboard');

    Route::get('/vendors', [ManageUserAdminController::class, 'vendorsIndex'])->name('vendors.index');
    Route::get('/vendors/{vendor}', [ManageUserAdminController::class, 'showVendor'])->name('vendors.show');
    Route::put('/vendors/{vendor}/approve', [ManageUserAdminController::class, 'approveVendor'])->name('vendors.approve');
    Route::put('/vendors/{vendor}/reject', [ManageUserAdminController::class, 'rejectVendor'])->name('vendors.reject');

    Route::get('/users', [ManageUserAdminController::class, 'usersIndex'])->name('users.index');
    Route::get('/users/{user}', [ManageUserAdminController::class, 'showUser'])->name('users.show');
    Route::put('/users/{user}/status', [ManageUserAdminController::class, 'toggleUserStatus'])->name('users.status');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password.update');
});

// Vendor routes
Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    // Vendor dashboard
    Route::get('/dashboard', [DashboardVendorController::class, 'showDashboard'])->name('dashboard');

    // Additional vendor routes can be added here
    Route::get('/services', [ServiceVendorController::class, 'index'])->name('service.index');
    Route::get('/services/create', [ServiceVendorController::class, 'create'])->name('service.create');
    Route::post('/services', [ServiceVendorController::class, 'store'])->name('service.store');
    Route::get('/services/{service}', [ServiceVendorController::class, 'show'])->name('service.show');
    Route::get('/services/{service}/edit', [ServiceVendorController::class, 'edit'])->name('service.edit');
    Route::put('/services/{service}', [ServiceVendorController::class, 'update'])->name('service.update');
    Route::delete('/services/{service}', [ServiceVendorController::class, 'destroy'])->name('service.destroy');

    Route::resource('bookings', BookingVendorController::class)->names('booking');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notification.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notification.show');
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notification.mark-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notification.destroy');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password.update');
});

// Couple routes
Route::middleware(['auth', 'role:couple'])->prefix('couple')->name('couple.')->group(function () {
    // Couple dashboard
    Route::get('/dashboard', [DashboardCoupleController::class, 'showCoupleDashboard'])->name('dashboard');

    // Budget management
    Route::get('/budget', [BudgetController::class, 'index'])->name('budget.index');
    Route::get('/budget/print', [BudgetController::class, 'printReport'])->name('budget.print');
    Route::get('/budget/create', [BudgetController::class, 'displayAddCategoryForm'])->name('budget.create');
    Route::post('/budget/create/added', [BudgetController::class, 'store'])->name('budget.store');
    Route::get('/budget/{budgetCategory}', [BudgetController::class, 'show'])->name('budget.show');
    Route::put('/budget/{budgetCategory}', [BudgetController::class, 'update'])->name('budget.update');
    Route::delete('/budget/{budgetCategory}', [BudgetController::class, 'destroy'])->name('budget.destroy');
    Route::put('/budget/alert', [BudgetController::class, 'updateLimit'])->name('budget.limit');

    // Expense management into Budget categories can be added here
    Route::get('/budget/{budgetCategory}/expenses', [BudgetController::class, 'showExpenses'])->name('budget.expenses');
    Route::put('/budget/{budgetCategory}/expenses/{expense}/complete', [BudgetController::class, 'completedExpense'])->name('budget.expenses.complete');
    Route::get('/budget/{budgetCategory}/expenses/create', [BudgetController::class, 'displayAddExpenseForm'])->name('budget.expenses.create');
    Route::post('/budget/{budgetCategory}/expenses/added', [BudgetController::class, 'addExpense'])->name('budget.expenses.add');
    Route::get('/budget/{budgetCategory}/expenses/{expense}', [BudgetController::class, 'showExpense'])->name('budget.expenses.show');
    Route::put('/budget/{budgetCategory}/expenses/{expense}', [BudgetController::class, 'updateExpense'])->name('budget.expenses.update');
    Route::put('/budget/{budgetCategory}/expenses/{expense}/due-date', [BudgetController::class, 'dueDateExpense'])->name('budget.expenses.due-date');
    Route::delete('/budget/{budgetCategory}/expenses/{expense}', [BudgetController::class, 'destroyExpense'])->name('budget.expenses.delete');

    // Guest management routes can be added here
    Route::get('/guests', [GuestController::class, 'showGuests'])->name('guests.index');
    Route::get('/guests/print', [GuestController::class, 'printReport'])->name('guests.print');
    Route::get('/guests/create', [GuestController::class, 'displayAddGuestForm'])->name('guests.create');
    Route::post('/guests/create/added', [GuestController::class, 'store'])->name('guests.store');
    Route::get('/guests/{guest}', [GuestController::class, 'show'])->name('guests.show');
    Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
    Route::put('/guests/{guest}/rsvp', [GuestController::class, 'updateRSVP'])->name('guests.rsvp');
    Route::post('/guests/{guest}/check-in', [GuestController::class, 'checkin'])->name('guests.checkin');
    Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');

    // Task management routes can be added here
    Route::get('/tasks', [TaskController::class, 'showTasks'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'displayAddTaskForm'])->name('tasks.create');
    Route::post('/tasks/create/added', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}/complete', [TaskController::class, 'markComplete'])->name('tasks.complete');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Vendor list routes (browse approved vendors and services)
    Route::get('/vendors', [VendorListController::class, 'index'])->name('vendorlist.index');
    Route::get('/vendors/{service}', [VendorListController::class, 'show'])->name('vendorlist.show');

    // AI Budget Estimation routes
    Route::get('/ai/budget-estimation', [AiBudgetController::class, 'index'])->name('ai.budget-estimation');
    Route::post('/ai/budget-estimation/estimate', [AiBudgetController::class, 'estimateInitial'])->name('ai.budget-estimation.estimate');
    Route::post('/ai/budget-estimation/chat', [AiBudgetController::class, 'chat'])->name('ai.budget-estimation.chat');

    // Setting : update profile, change password,  etc. can be added here
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password.update');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsReadAjax'])->name('notifications.mark-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});
