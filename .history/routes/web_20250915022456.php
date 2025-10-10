<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;

// Welcome page as home
Route::get('/', function () {
    return view('welcome');
});

// Guest Routes
Route::prefix('guest')->name('guest.')->middleware(['auth', 'role:guest'])->group(function () {
    Route::get('/dashboard', [GuestController::class, 'dashboard'])->name('dashboard');
    
    // Rooms and Booking Routes
    Route::get('/rooms', [RoomController::class, 'browse'])->name('rooms.browse');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/rooms/{room}/book', [BookingController::class, 'showBookingForm'])->name('rooms.book');
    Route::post('/rooms/{room}/book', [BookingController::class, 'store'])->name('rooms.book.store');
    
    // Bookings Management
    Route::get('/bookings', [BookingController::class, 'myBookings'])->name('bookings');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup.post');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,manager'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Room Management
    Route::resource('rooms', RoomController::class);
    Route::post('rooms/{room}/toggle-availability', [RoomController::class, 'toggleAvailability'])
        ->name('rooms.toggle-availability');
    Route::delete('rooms/{room}/images/{image}', [RoomController::class, 'deleteImage'])
        ->name('rooms.deleteImage');

    // Booking Management
    Route::controller(\App\Http\Controllers\Admin\BookingController::class)->group(function () {
        Route::get('/bookings', 'index')->name('bookings');
        Route::get('/bookings/{booking}', 'show')->name('bookings.show');
        Route::patch('/bookings/{booking}/status', 'updateStatus')->name('bookings.status');
    });

    // User Management
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users');
        Route::get('/users/create', 'create')->name('users.create');
        Route::post('/users', 'store')->name('users.store');
        Route::get('/users/{id}', 'show')->name('users.show');
        Route::put('/users/{id}', 'update')->name('users.update');
        Route::delete('/users/{id}', 'destroy')->name('users.destroy');
        Route::patch('/users/{id}/status', 'toggleStatus')->name('users.toggle');
        Route::patch('/users/{id}/block', 'blockUser')->name('users.block');
    });
});

// Staff Routes
Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:staff'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');

    // Rooms Management
    Route::get('/rooms', [RoomController::class, 'staffIndex'])->name('rooms.index');
    Route::get('/rooms/{room}', [RoomController::class, 'staffShow'])->name('rooms.show');
    
    // Bookings Management
    Route::get('/bookings', [BookingController::class, 'staffIndex'])->name('bookings.index');
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');
    
    // Food Menu Management - Commented out until MenuController is created
    // Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    // Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    // Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
    // Route::get('/menu/{menu}', [MenuController::class, 'show'])->name('menu.show');
    // Route::put('/menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
    // Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
});
