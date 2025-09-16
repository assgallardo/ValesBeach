<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminRoomController;

// TEMPORARY: Emergency admin creation (DELETE after use)
if (file_exists(__DIR__ . '/temp_admin.php')) {
    require __DIR__ . '/temp_admin.php';
}

// Welcome page as home
Route::get('/', function () {
    return view('welcome');
});

// Guest Routes - Protected by guest role middleware
Route::prefix('guest')->name('guest.')->middleware(['auth', 'role:guest'])->group(function () {
    Route::get('/dashboard', [GuestController::class, 'dashboard'])->name('dashboard');
    
    // Rooms and Booking Routes
    Route::get('/rooms', [BookingController::class, 'index'])->name('rooms');
    Route::get('/rooms/{room}/book', [BookingController::class, 'showBookingForm'])->name('rooms.book');
    Route::post('/rooms/{room}/book', [BookingController::class, 'store'])->name('rooms.book.store');
    Route::get('/bookings', [BookingController::class, 'myBookings'])->name('bookings');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// Authentication Routes
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.post');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes - Check user status for all authenticated routes
Route::middleware(['auth', 'user.status'])->group(function () {

    // Dashboard accessible by all authenticated users
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Admin and Manager Routes
    Route::middleware(['role:admin,manager'])->group(function () {
        // Bookings Management
        Route::get('/admin/bookings', [BookingController::class, 'manageBookings'])->name('admin.bookings');
        Route::post('/admin/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('admin.bookings.status');
        Route::get('/admin/bookings/{booking}', [BookingController::class, 'adminShow'])->name('admin.bookings.show');

        // Rooms Management
        Route::get('/admin/rooms', [AdminRoomController::class, 'index'])->name('admin.rooms');
        Route::get('/admin/rooms/create', [AdminRoomController::class, 'create'])->name('admin.rooms.create');
        Route::post('/admin/rooms', [AdminRoomController::class, 'store'])->name('admin.rooms.store');
        Route::get('/admin/rooms/{room}/edit', [AdminRoomController::class, 'edit'])->name('admin.rooms.edit');
        Route::put('/admin/rooms/{room}', [AdminRoomController::class, 'update'])->name('admin.rooms.update');
        Route::delete('/admin/rooms/{room}', [AdminRoomController::class, 'destroy'])->name('admin.rooms.destroy');
        Route::post('/admin/rooms/{room}/toggle-availability', [AdminRoomController::class, 'toggleAvailability'])->name('admin.rooms.toggle-availability');
    });

    // Admin-only routes (User Management)
    Route::middleware(['role:admin'])->group(function () {
        // User Management
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
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
