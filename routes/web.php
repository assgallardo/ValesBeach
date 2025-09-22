<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;

// Test route to check system status
Route::get('/test-system', function () {
    try {
        $userCount = \App\Models\User::count();
        $bookingCount = \App\Models\Booking::count();
        $roomCount = \App\Models\Room::count();
        
        return response('
<!DOCTYPE html>
<html>
<head><title>System Test</title></head>
<body style="font-family: Arial; padding: 20px;">
    <h1>✅ System Status Test</h1>
    <p>Users: ' . $userCount . '</p>
    <p>Bookings: ' . $bookingCount . '</p>
    <p>Rooms: ' . $roomCount . '</p>
    <p>Database: Working</p>
    <p>Laravel: Running</p>
    
    <h2>Test Logins:</h2>
    <a href="/test-login/admin" style="display: block; margin: 5px 0; padding: 10px; background: #16a34a; color: white; text-decoration: none;">Test Admin Login</a>
    <a href="/test-login/manager" style="display: block; margin: 5px 0; padding: 10px; background: #2563eb; color: white; text-decoration: none;">Test Manager Login</a>
    <a href="/test-login/staff" style="display: block; margin: 5px 0; padding: 10px; background: #dc2626; color: white; text-decoration: none;">Test Staff Login</a>
    
    <h2>Direct Access Tests:</h2>
    <a href="/admin/dashboard" style="display: block; margin: 5px 0;">Admin Dashboard</a>
    <a href="/admin/bookings" style="display: block; margin: 5px 0;">Admin Bookings</a>
    <a href="/admin/users" style="display: block; margin: 5px 0;">Admin Users (should fail for staff)</a>
</body>
</html>');
    } catch (\Exception $e) {
        return response('Error: ' . $e->getMessage(), 500);
    }
});

// Test route for role-based admin login
Route::get('/test-login/{role}', function ($role) {
    try {
        // Find user by role
        $user = \App\Models\User::where('role', $role)->first();
        
        if (!$user) {
            return response('No ' . $role . ' user found', 404);
        }
        
        // Log in the user
        \Illuminate\Support\Facades\Auth::login($user);
        
        return redirect('/admin/dashboard');
        
    } catch (\Exception $e) {
        return response('Login Error: ' . $e->getMessage(), 500);
    }
});

// Welcome page as home
Route::get('/', function () {
    return view('welcome');
});

// Guest Routes
Route::prefix('guest')->name('guest.')->middleware(['auth', 'user.status', 'role:guest'])->group(function () {
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

// Admin Routes - Accessible by admin, manager, and staff (except user management)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'user.status', 'role:admin,manager,staff'])->group(function () {
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

    // Reservation Management (Primary booking interface)
    Route::controller(\App\Http\Controllers\Admin\BookingController::class)->group(function () {
        Route::get('/reservations', 'reservations')->name('reservations');
        Route::get('/reservations/create', 'create')->name('reservations.create');
        Route::get('/reservations/create-from-room/{room}', 'createFromRoom')->name('reservations.createFromRoom');
        Route::post('/reservations/store', 'store')->name('reservations.store');
        Route::get('/reservations/{booking}', 'show')->name('reservations.show');
        Route::patch('/reservations/{booking}/status', 'updateStatus')->name('reservations.status');
    });

    // Legacy booking routes (redirect to reservations)
    Route::get('/bookings', function() {
        return redirect()->route('admin.reservations');
    })->name('bookings');
    
    Route::get('/bookings/create', function() {
        return redirect()->route('admin.reservations.create');
    })->name('bookings.create');
    
    Route::get('/bookings/create-from-room/{room}', function($room) {
        return redirect()->route('admin.reservations.createFromRoom', $room);
    })->name('bookings.createFromRoom');
    
    Route::post('/bookings/store', function() {
        return redirect()->route('admin.reservations.create');
    })->name('bookings.store');
    
    Route::get('/bookings/{booking}', function($booking) {
        return redirect()->route('admin.reservations.show', $booking);
    })->name('bookings.show');
    
    Route::patch('/bookings/{booking}/status', [\App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('bookings.status');
    
    // Calendar view for bookings
    Route::get('/calendar', [\App\Http\Controllers\Admin\BookingController::class, 'calendar'])->name('calendar');
});

// Admin-only Routes - User Management (restricted to admin only)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'user.status', 'role:admin'])->group(function () {
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
        Route::patch('/users/{id}/unblock', 'unblockUser')->name('users.unblock');
    });
});

// Staff Routes - Redirect to admin dashboard since staff now has access to admin features
Route::prefix('staff')->name('staff.')->middleware(['auth', 'user.status', 'role:staff'])->group(function () {
    // Staff dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('dashboard');
});
