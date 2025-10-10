<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Manager\ServiceRequestController as ManagerServiceRequestController;
use App\Http\Controllers\FoodOrderController;
use App\Http\Controllers\GuestServiceController;
use App\Http\Controllers\Manager\StaffAssignmentController;

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
    <a href="/admin/reservations" style="display: block; margin: 5px 0;">Admin Reservations</a>
    <a href="/manager/dashboard" style="display: block; margin: 5px 0;">Manager Dashboard</a>
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
        
        // Redirect based on role
        if ($role === 'manager') {
            return redirect('/manager/dashboard');
        } else {
            return redirect('/admin/dashboard');
        }
        
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
    Route::get('/rooms', [GuestController::class, 'browseRooms'])->name('rooms.browse');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/rooms/{room}/book', [BookingController::class, 'showBookingForm'])->name('rooms.book');
    Route::post('/rooms/{room}/book', [BookingController::class, 'store'])->name('rooms.book.store');
    
    // Bookings Management
    Route::get('/bookings', [BookingController::class, 'myBookings'])->name('bookings');
    
    // Booking History (must come before parameterized routes)
    Route::get('/bookings/history', [BookingController::class, 'history'])->name('bookings.history');
    
    // Parameterized booking routes (must come after specific routes)
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    
    // Services and Service Requests
    Route::prefix('services')->name('services.')->group(function () {
        // Browse available services
        Route::get('/', [GuestServiceController::class, 'index'])->name('index');
        Route::get('/{service}', [GuestServiceController::class, 'show'])->name('show');
        
        // Service request creation
        Route::get('/{service}/request', [GuestServiceController::class, 'create'])->name('request');
        Route::post('/{service}/request', [GuestServiceController::class, 'store'])->name('request.store');
        
        // Service request history
        Route::get('/requests/history', [GuestServiceController::class, 'history'])->name('requests.history');
    });
    
    // Food Ordering Routes
    Route::prefix('food-orders')->name('food-orders.')->group(function () {
        // Menu browsing
        Route::get('/menu', [FoodOrderController::class, 'menu'])->name('menu');
        
        // Cart management
        Route::get('/cart', [FoodOrderController::class, 'cart'])->name('cart');
        Route::post('/cart/add', [FoodOrderController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/update', [FoodOrderController::class, 'updateCart'])->name('cart.update');
        Route::get('/cart/count', [FoodOrderController::class, 'cartCount'])->name('cart.count');
        
        // Checkout and order placement
        Route::get('/checkout', [FoodOrderController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [FoodOrderController::class, 'placeOrder'])->name('place-order');
        
        // Order management
        Route::get('/orders', [FoodOrderController::class, 'orders'])->name('orders');
        Route::get('/orders/{foodOrder}', [FoodOrderController::class, 'show'])->name('show');
        Route::post('/orders/{foodOrder}/cancel', [FoodOrderController::class, 'cancel'])->name('cancel');
    });
});

// Payment Routes - Accessible by authenticated users
Route::middleware(['auth', 'user.status'])->group(function () {
    // Payment processing
    Route::get('/bookings/{booking}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/confirmation', [PaymentController::class, 'confirmation'])->name('payments.confirmation');
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    
    // Invoice management
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/bookings/{booking}/invoice/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
});

// Admin Payment & Billing Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'user.status', 'role:admin,manager'])->group(function () {
    // Payment management
    Route::get('/payments', [PaymentController::class, 'adminIndex'])->name('payments.index');
    Route::patch('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');
    Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    
    // Invoice management
    Route::get('/invoices', [InvoiceController::class, 'adminIndex'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
    Route::post('/invoices/{invoice}/remind', [InvoiceController::class, 'sendReminder'])->name('invoices.remind');
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
    Route::controller(AdminBookingController::class)->group(function () {
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
    
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
    
    // Calendar view for bookings
    Route::get('/calendar', [AdminBookingController::class, 'calendar'])->name('calendar');
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
    // Staff dashboard - redirect to admin dashboard
    Route::get('/dashboard', function() {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');
});

// Manager Routes - Accessible by manager and admin
Route::prefix('manager')->name('manager.')->middleware(['auth', 'user.status', 'role:manager,admin'])->group(function () {
    // Service Request routes - use the Manager namespace
    Route::get('/service-requests', [ManagerServiceRequestController::class, 'index'])->name('service-requests.index');
    Route::get('/service-requests/{serviceRequest}', [ManagerServiceRequestController::class, 'show'])->name('service-requests.show');
    Route::patch('/service-requests/{serviceRequest}/status', [ManagerServiceRequestController::class, 'updateStatus'])->name('service-requests.updateStatus');
    
    // Other manager routes...
    Route::get('/dashboard', [App\Http\Controllers\ManagerController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings', [App\Http\Controllers\ManagerController::class, 'bookings'])->name('bookings.index');
    Route::get('/bookings/create', [App\Http\Controllers\ManagerController::class, 'createBooking'])->name('bookings.create');
    Route::post('/bookings', [App\Http\Controllers\ManagerController::class, 'storeBooking'])->name('bookings.store');
    Route::get('/bookings/{id}', [App\Http\Controllers\ManagerController::class, 'showBooking'])->name('bookings.show');
    
    // ADD THESE ROUTES FOR EDIT FUNCTIONALITY:
    Route::get('/bookings/{id}/edit', [App\Http\Controllers\ManagerController::class, 'editBooking'])->name('bookings.edit');
    Route::put('/bookings/{id}', [App\Http\Controllers\ManagerController::class, 'updateBooking'])->name('bookings.update');
    
    // Booking status updates
    Route::patch('/bookings/{id}/status', [App\Http\Controllers\ManagerController::class, 'updateBookingStatus'])->name('bookings.updateStatus');
    
    // Other booking actions
    Route::patch('/bookings/{id}/confirm', [App\Http\Controllers\ManagerController::class, 'confirmBooking'])->name('bookings.confirm');
    Route::patch('/bookings/{id}/checkin', [App\Http\Controllers\ManagerController::class, 'checkinBooking'])->name('bookings.checkin');
    Route::patch('/bookings/{id}/checkout', [App\Http\Controllers\ManagerController::class, 'checkoutBooking'])->name('bookings.checkout');
    Route::patch('/bookings/{id}/cancel', [App\Http\Controllers\ManagerController::class, 'cancelBooking'])->name('bookings.cancel');
    
    // Quick book from room
    Route::get('/bookings/create/{room}', [App\Http\Controllers\ManagerController::class, 'createFromRoom'])->name('bookings.createFromRoom');
    
    // Services - Either use resource or simple routes
    Route::resource('services', App\Http\Controllers\Manager\ServiceController::class);
    // OR if you prefer simple routes:
    // Route::get('/services', [App\Http\Controllers\ManagerController::class, 'services'])->name('services.index');
    
    // Other management routes
    Route::get('/rooms', [App\Http\Controllers\ManagerController::class, 'rooms'])->name('rooms');
    Route::get('/staff', [App\Http\Controllers\ManagerController::class, 'staff'])->name('staff');
    Route::get('/guests', [App\Http\Controllers\ManagerController::class, 'guests'])->name('guests');
    Route::get('/reports', [App\Http\Controllers\ManagerController::class, 'reports'])->name('reports');
    Route::get('/maintenance', [App\Http\Controllers\ManagerController::class, 'maintenance'])->name('maintenance');
    
    // Toggle service status
    Route::patch('/services/{service}/toggle-status', [App\Http\Controllers\Manager\ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    
    // Staff Assignment Routes - ENHANCED
    Route::prefix('staff-assignment')->name('staff-assignment.')->group(function () {
        Route::get('/', [StaffAssignmentController::class, 'index'])->name('index');
        Route::get('/{serviceRequest}/edit', [StaffAssignmentController::class, 'edit'])->name('edit');
        Route::put('/{serviceRequest}', [StaffAssignmentController::class, 'update'])->name('update');
        Route::post('/assign/{serviceRequest}', [StaffAssignmentController::class, 'assign'])->name('assign');
        Route::delete('/unassign/{serviceRequest}', [StaffAssignmentController::class, 'unassign'])->name('unassign');
        Route::patch('/{serviceRequest}/status', [StaffAssignmentController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{serviceRequest}', [StaffAssignmentController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-assign', [StaffAssignmentController::class, 'bulkAssign'])->name('bulk-assign');
    });
});

// Guest Services Routes
Route::middleware(['auth'])->prefix('guest')->name('guest.')->group(function () {
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [GuestServiceController::class, 'index'])->name('index');
        Route::get('/{service}', [GuestServiceController::class, 'show'])->name('show');
        Route::get('/{service}/request', [GuestServiceController::class, 'create'])->name('request');
        Route::post('/{service}/request', [GuestServiceController::class, 'store'])->name('request.store');
        Route::get('/requests/history', [GuestServiceController::class, 'history'])->name('requests.history');
        Route::patch('/requests/{serviceRequest}/cancel', [GuestServiceController::class, 'cancel'])->name('cancel');
    });
});

// New guest service request routes
Route::get('/services/{service}/request', [GuestServiceController::class, 'create'])->name('guest.services.request');
Route::post('/services/{service}/request', [GuestServiceController::class, 'store'])->name('guest.services.request.store');

// Guest Routes - Update these routes to use GuestServiceController
Route::middleware(['auth', 'user.status'])->group(function () {
    // Guest Service Routes - MAKE SURE TO USE GuestServiceController
    Route::prefix('guest')->name('guest.')->group(function () {
        Route::get('/services', [\App\Http\Controllers\GuestServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{service}', [\App\Http\Controllers\GuestServiceController::class, 'show'])->name('services.show');
        Route::get('/services/{service}/request', [\App\Http\Controllers\GuestServiceController::class, 'create'])->name('services.request');
        Route::post('/services/{service}/request', [\App\Http\Controllers\GuestServiceController::class, 'store'])->name('services.request.store');
        Route::get('/service-requests/history', [\App\Http\Controllers\GuestServiceController::class, 'history'])->name('services.requests.history');
        Route::patch('/service-requests/{serviceRequest}/cancel', [\App\Http\Controllers\GuestServiceController::class, 'cancel'])->name('services.requests.cancel');
    });
});

// Manager Routes
Route::prefix('manager')->name('manager.')->middleware(['auth', 'user.status', 'role:manager,admin'])->group(function () {
    // Manager Service Routes
    Route::resource('services', \App\Http\Controllers\Manager\ServiceController::class);
    Route::post('/services/{service}/toggle-status', [\App\Http\Controllers\Manager\ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    
    // Staff Assignment Routes
    Route::prefix('staff-assignment')->name('staff-assignment.')->group(function () {
        Route::get('/', [StaffAssignmentController::class, 'index'])->name('index');
        Route::get('/{serviceRequest}/edit', [StaffAssignmentController::class, 'edit'])->name('edit');
        Route::put('/{serviceRequest}', [StaffAssignmentController::class, 'update'])->name('update');
        Route::post('/assign/{serviceRequest}', [StaffAssignmentController::class, 'assign'])->name('assign');
        Route::delete('/unassign/{serviceRequest}', [StaffAssignmentController::class, 'unassign'])->name('unassign');
        Route::patch('/{serviceRequest}/status', [StaffAssignmentController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{serviceRequest}', [StaffAssignmentController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-assign', [StaffAssignmentController::class, 'bulkAssign'])->name('bulk-assign');
    });
});

// ADD THIS EXPLICIT ROUTE RIGHT HERE
Route::post('guest/services/submit', [App\Http\Controllers\GuestServiceController::class, 'store'])
    ->name('guest.services.store')
    ->middleware(['auth', 'user.status', 'role:guest']);
