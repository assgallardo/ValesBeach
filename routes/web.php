<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// TEMPORARY: Emergency admin creation (DELETE after use)
if (file_exists(__DIR__ . '/temp_admin.php')) {
    require __DIR__ . '/temp_admin.php';
}

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.post');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Protected Routes - Check user status for all authenticated routes
Route::middleware(['auth', 'user.status'])->group(function () {

    // Dashboard accessible by all authenticated users
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Admin-only routes (User Management)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{id}', [UserController::class, 'show'])->name('admin.users.show');
        Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::patch('/admin/users/{id}/status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle');
        Route::patch('/admin/users/{id}/block', [UserController::class, 'blockUser'])->name('admin.users.block');
    });

    // Manager and Admin routes (Reports & Analytics, Settings)
    Route::middleware(['role:admin,manager'])->group(function () {
        // Future routes for reports, analytics, and settings
        // Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports');
        // Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings');
    });

    // All authenticated users (Staff, Manager, Admin) - Bookings, Rooms, Food Menu
    Route::middleware(['role:admin,manager,staff'])->group(function () {
        // Future routes for bookings, rooms, and food menu management
        // Route::get('/admin/bookings', [BookingController::class, 'index'])->name('admin.bookings');
        // Route::get('/admin/rooms', [RoomController::class, 'index'])->name('admin.rooms');
        // Route::get('/admin/menu', [MenuController::class, 'index'])->name('admin.menu');
    });
});
