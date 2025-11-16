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
use App\Http\Controllers\Manager\PaymentController as ManagerPaymentController;
use App\Http\Controllers\Manager\ReportsController as ManagerReportsController;
use App\Http\Controllers\FoodOrderController;
use App\Http\Controllers\GuestServiceController;
use App\Http\Controllers\Manager\StaffAssignmentController;
use App\Http\Controllers\Staff\MenuController as StaffMenuController;
use App\Http\Controllers\Staff\FoodOrderController as StaffFoodOrderController;

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
Route::prefix('guest')->name('guest.')->middleware(['auth', 'user.status'])->group(function () {
    
    // Routes accessible by all authenticated users (viewing)
    Route::middleware('role:guest,admin,manager,staff')->group(function () {
        // Rooms Browsing
        Route::get('/rooms', [GuestController::class, 'browseRooms'])->name('rooms.browse');
        Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    });
    
    // Guest-only routes (booking and management)
    Route::middleware('role:guest')->group(function () {
        Route::get('/dashboard', [GuestController::class, 'dashboard'])->name('dashboard');
        
        // Room Booking Routes (Guest only)
        Route::get('/rooms/{room}/book', [BookingController::class, 'showBookingForm'])->name('rooms.book');
        Route::post('/rooms/{room}/book', [BookingController::class, 'store'])->name('rooms.book.store');
        
        // Bookings Management (Rooms)
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
    }); // End of guest-only middleware group
}); // End of guest routes group

// Payment Routes - Accessible by authenticated users
Route::middleware(['auth', 'user.status'])->group(function () {
    // Payment processing
    Route::get('/bookings/{booking}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store'])->name('payments.store');
    // Specific routes must come before parameterized routes
    Route::post('/payments/bulk-update-method', [PaymentController::class, 'bulkUpdatePaymentMethod'])->name('payments.bulkUpdateMethod');
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
    Route::get('/payments/completed', [PaymentController::class, 'completed'])->name('payments.completed');
    Route::get('/payments/completed/details', [PaymentController::class, 'completedDetails'])->name('payments.completed.details');
    Route::get('/payments/{payment}/confirmation', [PaymentController::class, 'confirmation'])->name('payments.confirmation');
    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::patch('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    // This must be last to avoid catching specific routes
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    
    // Invoice management
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::match(['get', 'post'], '/bookings/{booking}/invoice/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::post('/invoices/generate-combined', [InvoiceController::class, 'generateCombined'])->name('invoices.generate-combined');
    Route::get('/invoices/combined', [InvoiceController::class, 'showCombined'])->name('invoices.show-combined');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
});

// Admin Payment & Billing Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'user.status', 'role:admin,manager,staff'])->group(function () {
    // Payment management
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/completed', [PaymentController::class, 'adminCompleted'])->name('payments.completed');
    Route::get('/payments/customer/{user}', [PaymentController::class, 'showCustomerPayments'])->name('payments.customer');
    Route::post('/payments/customer/{user}/complete', [PaymentController::class, 'completeAllCustomerPayments'])->name('payments.customer.complete');
    Route::post('/payments/customer/{user}/revert', [PaymentController::class, 'revertAllCustomerPayments'])->name('payments.customer.revert');
    Route::get('/payments/completed/customer/{user}', [PaymentController::class, 'showCompletedCustomerPayments'])->name('payments.completed.customer');
    Route::get('/payments/customer/{user}/invoice', [PaymentController::class, 'generateCustomerInvoice'])->name('payments.customer.invoice');
    Route::post('/payments/customer/{user}/invoice', [PaymentController::class, 'saveCustomerInvoice'])->name('payments.customer.invoice.save');
    Route::get('/invoices/{invoice}/edit', [PaymentController::class, 'editCustomerInvoice'])->name('invoices.edit');
    Route::patch('/invoices/{invoice}', [PaymentController::class, 'updateCustomerInvoice'])->name('invoices.update');
    Route::get('/payments/{payment}', [PaymentController::class, 'adminShow'])->name('payments.show');
    Route::patch('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.status');
    
    // Refund management
    Route::get('/payments/{payment}/refund', [PaymentController::class, 'showRefundForm'])->name('payments.refund.form');
    Route::post('/payments/{payment}/refund', [PaymentController::class, 'processRefund'])->name('payments.refund.process');
    // Cancel refund (restore original amount)
    Route::post('/payments/{payment}/cancel-refund', [PaymentController::class, 'cancelRefund'])->name('payments.cancelRefund');
    
    // Delete extra charge payment
    Route::delete('/payments/{payment}/extra-charge', [PaymentController::class, 'deleteExtraCharge'])->name('payments.extraCharge.delete');
    
    // Invoice management
    Route::get('/invoices', [InvoiceController::class, 'adminIndex'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
    Route::post('/invoices/{invoice}/remind', [InvoiceController::class, 'sendReminder'])->name('invoices.remind');
    
    // Customer Reports routes (NEW) - Admin uses same controller as Manager
    Route::get('/reports/customer-analytics', [ManagerReportsController::class, 'customerAnalytics'])->name('reports.customer-analytics');
    Route::get('/reports/repeat-customers', [ManagerReportsController::class, 'repeatCustomers'])->name('reports.repeat-customers');
    Route::get('/reports/customer-preferences', [ManagerReportsController::class, 'customerPreferences'])->name('reports.customer-preferences');
    Route::get('/reports/payment-methods', [ManagerReportsController::class, 'paymentMethods'])->name('reports.payment-methods');
    
    // Housekeeping Management routes (NEW) - Admin access
    Route::get('/housekeeping', [App\Http\Controllers\Manager\HousekeepingController::class, 'index'])->name('housekeeping.index');
    Route::post('/housekeeping/{housekeeping}/assign', [App\Http\Controllers\Manager\HousekeepingController::class, 'assign'])->name('housekeeping.assign');
    Route::post('/housekeeping/{housekeeping}/status', [App\Http\Controllers\Manager\HousekeepingController::class, 'updateStatus'])->name('housekeeping.status');
    Route::post('/housekeeping/{housekeeping}/priority', [App\Http\Controllers\Manager\HousekeepingController::class, 'updatePriority'])->name('housekeeping.priority');
    Route::post('/housekeeping/{housekeeping}/notes', [App\Http\Controllers\Manager\HousekeepingController::class, 'addNotes'])->name('housekeeping.notes');
    Route::delete('/housekeeping/{housekeeping}', [App\Http\Controllers\Manager\HousekeepingController::class, 'destroy'])->name('housekeeping.destroy');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup.post');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Admin Routes - Accessible by admin, manager, and staff
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
        Route::post('/reservations/check-availability', 'checkAvailability')->name('reservations.checkAvailability');
        Route::get('/reservations/{booking}', 'show')->name('reservations.show');
        Route::get('/reservations/{booking}/edit', 'edit')->name('reservations.edit');
        Route::put('/reservations/{booking}', 'update')->name('reservations.update');
        Route::patch('/reservations/{booking}/status', 'updateStatus')->name('reservations.status');
        Route::patch('/reservations/{booking}/payment-status', 'updatePaymentStatus')->name('reservations.payment-status');
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
    Route::patch('/bookings/{booking}/payment-status', [AdminBookingController::class, 'updatePaymentStatus'])->name('bookings.payment-status');
    
    // Reports & Analytics routes - Same as Manager
    Route::get('/reports', [ManagerReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/service-usage', [ManagerReportsController::class, 'serviceUsage'])->name('reports.service-usage');
    Route::get('/reports/performance-metrics', [ManagerReportsController::class, 'performanceMetrics'])->name('reports.performance-metrics');
    Route::get('/reports/staff-performance', [ManagerReportsController::class, 'staffPerformance'])->name('reports.staff-performance');
    Route::get('/reports/export', [ManagerReportsController::class, 'export'])->name('reports.export');
    
    // Sales Reports routes - Same as Manager
    Route::get('/reports/room-sales', [ManagerReportsController::class, 'roomSales'])->name('reports.room-sales');
    Route::get('/reports/food-sales', [ManagerReportsController::class, 'foodSales'])->name('reports.food-sales');
    Route::get('/reports/service-sales', [ManagerReportsController::class, 'serviceSales'])->name('reports.service-sales');
    
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
Route::prefix('staff')->name('staff.')->middleware(['auth', 'user.status', 'role:staff,manager,admin'])->group(function () {
    // Staff dashboard - redirect to admin dashboard
    Route::get('/dashboard', function() {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');
    
    // Calendar view for bookings
    Route::get('/calendar', [App\Http\Controllers\ManagerController::class, 'calendar'])->name('calendar');
});

// Manager Routes - Accessible by manager, admin, and staff
Route::prefix('manager')->name('manager.')->middleware(['auth', 'user.status', 'role:manager,admin,staff'])->group(function () {
    // Add this redirect route for backward compatibility
    Route::get('/reports', function() {
        return redirect()->route('manager.reports.index');
    })->name('reports');
    
    // Service Request routes - use the Manager namespace
    Route::get('/service-requests', [ManagerServiceRequestController::class, 'index'])->name('service-requests.index');
    Route::get('/service-requests/{serviceRequest}', [ManagerServiceRequestController::class, 'show'])->name('service-requests.show');
    Route::patch('/service-requests/{serviceRequest}/status', [ManagerServiceRequestController::class, 'updateStatus'])->name('service-requests.updateStatus');
    
    // Payment tracking routes - use the Manager namespace
    Route::get('/payments', [ManagerPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/completed', [ManagerPaymentController::class, 'completed'])->name('payments.completed');
    Route::get('/payments/customer/{user}', [ManagerPaymentController::class, 'showCustomerPayments'])->name('payments.customer');
    Route::post('/payments/customer/{user}/complete', [ManagerPaymentController::class, 'completeAllCustomerPayments'])->name('payments.customer.complete');
    Route::post('/payments/customer/{user}/revert', [ManagerPaymentController::class, 'revertAllCustomerPayments'])->name('payments.customer.revert');
    Route::get('/payments/customer/{user}/invoice', [PaymentController::class, 'generateCustomerInvoice'])->name('payments.customer.invoice');
    Route::post('/payments/customer/{user}/invoice', [PaymentController::class, 'saveCustomerInvoice'])->name('payments.customer.invoice.save');
    Route::get('/payments/completed/customer/{user}', [ManagerPaymentController::class, 'showCompletedCustomerPayments'])->name('payments.completed.customer');
    Route::get('/payments/{payment}', [ManagerPaymentController::class, 'show'])->name('payments.show');
    Route::patch('/payments/{payment}/status', [ManagerPaymentController::class, 'updateStatus'])->name('payments.status');
    Route::get('/payments-analytics', [ManagerPaymentController::class, 'analytics'])->name('payments.analytics');
    Route::get('/payments-export', [ManagerPaymentController::class, 'export'])->name('payments.export');
    
    // Service Reports routes - use the Manager namespace
    Route::get('/reports', [ManagerReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/service-usage', [ManagerReportsController::class, 'serviceUsage'])->name('reports.service-usage');
    Route::get('/reports/performance-metrics', [ManagerReportsController::class, 'performanceMetrics'])->name('reports.performance-metrics');
    Route::get('/reports/staff-performance', [ManagerReportsController::class, 'staffPerformance'])->name('reports.staff-performance');
    Route::get('/reports/export', [ManagerReportsController::class, 'export'])->name('reports.export');
    
    // Sales Reports routes
    Route::get('/reports/room-sales', [ManagerReportsController::class, 'roomSales'])->name('reports.room-sales');
    Route::get('/reports/food-sales', [ManagerReportsController::class, 'foodSales'])->name('reports.food-sales');
    Route::get('/reports/service-sales', [ManagerReportsController::class, 'serviceSales'])->name('reports.service-sales');
    
    // Customer Reports routes (NEW)
    Route::get('/reports/customer-analytics', [ManagerReportsController::class, 'customerAnalytics'])->name('reports.customer-analytics');
    Route::get('/reports/repeat-customers', [ManagerReportsController::class, 'repeatCustomers'])->name('reports.repeat-customers');
    Route::get('/reports/customer-preferences', [ManagerReportsController::class, 'customerPreferences'])->name('reports.customer-preferences');
    Route::get('/reports/payment-methods', [ManagerReportsController::class, 'paymentMethods'])->name('reports.payment-methods');
    
    // Housekeeping Management routes (NEW)
    Route::get('/housekeeping', [App\Http\Controllers\Manager\HousekeepingController::class, 'index'])->name('housekeeping.index');
    Route::post('/housekeeping/{housekeeping}/assign', [App\Http\Controllers\Manager\HousekeepingController::class, 'assign'])->name('housekeeping.assign');
    Route::post('/housekeeping/{housekeeping}/status', [App\Http\Controllers\Manager\HousekeepingController::class, 'updateStatus'])->name('housekeeping.status');
    Route::post('/housekeeping/{housekeeping}/priority', [App\Http\Controllers\Manager\HousekeepingController::class, 'updatePriority'])->name('housekeeping.priority');
    Route::post('/housekeeping/{housekeeping}/notes', [App\Http\Controllers\Manager\HousekeepingController::class, 'addNotes'])->name('housekeeping.notes');
    Route::delete('/housekeeping/{housekeeping}', [App\Http\Controllers\Manager\HousekeepingController::class, 'destroy'])->name('housekeeping.destroy');
    
    // Other manager routes...
    Route::get('/dashboard', [App\Http\Controllers\ManagerController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings', [App\Http\Controllers\ManagerController::class, 'bookings'])->name('bookings.index');
    Route::get('/bookings/create', [App\Http\Controllers\ManagerController::class, 'createBooking'])->name('bookings.create');
    Route::post('/bookings', [App\Http\Controllers\ManagerController::class, 'storeBooking'])->name('bookings.store');
    Route::post('/bookings/check-availability', [App\Http\Controllers\ManagerController::class, 'checkAvailability'])->name('bookings.checkAvailability');
    Route::get('/bookings/{id}', [App\Http\Controllers\ManagerController::class, 'showBooking'])->name('bookings.show');
    
    // ADD THESE ROUTES FOR EDIT FUNCTIONALITY:
    Route::get('/bookings/{id}/edit', [App\Http\Controllers\ManagerController::class, 'editBooking'])->name('bookings.edit');
    Route::put('/bookings/{id}', [App\Http\Controllers\ManagerController::class, 'updateBooking'])->name('bookings.update');
    
    // Booking status updates
    Route::patch('/bookings/{id}/status', [App\Http\Controllers\ManagerController::class, 'updateBookingStatus'])->name('bookings.updateStatus');
    Route::patch('/bookings/{id}/payment-status', [App\Http\Controllers\ManagerController::class, 'updatePaymentStatus'])->name('bookings.payment-status');
    
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
    
    // Rooms Resource Routes
    Route::resource('rooms', App\Http\Controllers\ManagerRoomController::class);
    Route::post('/rooms/{room}/toggle-availability', [App\Http\Controllers\ManagerRoomController::class, 'toggleAvailability'])->name('rooms.toggle-availability');
    Route::delete('/rooms/{room}/images/{image}', [App\Http\Controllers\ManagerRoomController::class, 'deleteImage'])->name('rooms.deleteImage');
    
    // Other management routes
    Route::get('/staff', [App\Http\Controllers\ManagerController::class, 'staff'])->name('staff');
    Route::get('/guests', [App\Http\Controllers\ManagerController::class, 'guests'])->name('guests');

    Route::get('/maintenance', [App\Http\Controllers\ManagerController::class, 'maintenance'])->name('maintenance');
    
    // Calendar management
    Route::get('/calendar', [App\Http\Controllers\ManagerController::class, 'calendar'])->name('calendar');
    
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
        Route::patch('/{serviceRequest}/cancel', [StaffAssignmentController::class, 'cancel'])->name('cancel');
        Route::patch('/{serviceRequest}/confirm-task', [StaffAssignmentController::class, 'confirmTask'])->name('confirm-task');
        Route::patch('/{serviceRequest}/quick-update', [StaffAssignmentController::class, 'quickUpdate'])->name('quick-update');
        Route::delete('/{serviceRequest}', [StaffAssignmentController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-assign', [StaffAssignmentController::class, 'bulkAssign'])->name('bulk-assign');
        
        // Housekeeping task routes
        Route::patch('/housekeeping/{task}', [StaffAssignmentController::class, 'updateHousekeepingTask'])->name('housekeeping.update');
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
        Route::patch('/{serviceRequest}/cancel', [StaffAssignmentController::class, 'cancel'])->name('cancel');
        Route::patch('/{serviceRequest}/confirm-task', [StaffAssignmentController::class, 'confirmTask'])->name('confirm-task');
        Route::patch('/{serviceRequest}/quick-update', [StaffAssignmentController::class, 'quickUpdate'])->name('quick-update');
        Route::delete('/{serviceRequest}', [StaffAssignmentController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-assign', [StaffAssignmentController::class, 'bulkAssign'])->name('bulk-assign');
    });
});

// Staff Routes - Task Management (accessible by staff, manager, and admin)
Route::prefix('staff')->name('staff.')->middleware(['auth', 'user.status', 'role:staff,manager,admin'])->group(function () {
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [App\Http\Controllers\StaffTaskController::class, 'index'])->name('index');
        Route::patch('/{task}/status', [App\Http\Controllers\StaffTaskController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{task}/notes', [App\Http\Controllers\StaffTaskController::class, 'updateNotes'])->name('update-notes');
        Route::post('/{task}/cancel', [App\Http\Controllers\StaffTaskController::class, 'cancel'])->name('cancel');
        Route::get('/{task}', [App\Http\Controllers\StaffTaskController::class, 'show'])->name('show');
    });
});


// Staff Routes - Menu Management (Staff can edit menu items)
Route::prefix('staff')->name('staff.')->middleware(['auth', 'user.status', 'role:staff,manager,admin'])->group(function () {
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [StaffMenuController::class, 'index'])->name('index');
        Route::get('/create', [StaffMenuController::class, 'create'])->name('create');
        Route::post('/', [StaffMenuController::class, 'store'])->name('store');
        Route::get('/{menuItem}/edit', [StaffMenuController::class, 'edit'])->name('edit');
        Route::put('/{menuItem}', [StaffMenuController::class, 'update'])->name('update');
        Route::delete('/{menuItem}', [StaffMenuController::class, 'destroy'])->name('destroy');
        Route::post('/{menuItem}/toggle-availability', [StaffMenuController::class, 'toggleAvailability'])->name('toggle-availability');
        Route::post('/{menuItem}/toggle-featured', [StaffMenuController::class, 'toggleFeatured'])->name('toggle-featured');
    });
});

// Staff & Manager Routes - Food Order Management (View and manage customer orders)
Route::prefix('staff')->name('staff.')->middleware(['auth', 'user.status', 'role:staff,manager,admin'])->group(function () {
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [StaffFoodOrderController::class, 'index'])->name('index');
        Route::get('/statistics', [StaffFoodOrderController::class, 'statistics'])->name('statistics');
        Route::get('/{foodOrder}', [StaffFoodOrderController::class, 'show'])->name('show');
        Route::post('/{foodOrder}/status', [StaffFoodOrderController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{foodOrder}/delete', [StaffFoodOrderController::class, 'destroy'])->name('delete');
    });
});
// ADD THIS EXPLICIT ROUTE RIGHT HERE
Route::post('guest/services/submit', [App\Http\Controllers\GuestServiceController::class, 'store'])
    ->name('guest.services.store')
    ->middleware(['auth', 'user.status', 'role:guest']);

// Guest service requests routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/guest/services/requests/history', [GuestServiceController::class, 'requestsHistory'])
        ->name('guest.services.requests.history');
    
    Route::get('/guest/service-requests/{id}', [GuestServiceController::class, 'showRequest'])
        ->name('guest.service-requests.show');
        
    Route::post('/guest/service-requests/{id}/cancel', [GuestServiceController::class, 'cancelRequest'])
        ->name('guest.service-requests.cancel');
    
    // Guest service routes
    Route::get('/guest/services', [GuestServiceController::class, 'index'])->name('guest.services.index');
    Route::get('/guest/services/history', [GuestServiceController::class, 'history'])->name('guest.services.history');
    Route::get('/guest/services/{service}', [GuestServiceController::class, 'show'])->name('guest.services.show');
    Route::post('/guest/services/request/{serviceRequest}/cancel', [GuestServiceController::class, 'cancel'])->name('guest.services.cancel');
});

// Guest routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Guest routes
    Route::prefix('guest')->name('guest.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [GuestController::class, 'dashboard'])->name('dashboard');
        
        // Services - SPECIFIC routes FIRST
        Route::get('/services', [GuestServiceController::class, 'index'])->name('services.index');
        Route::get('/services/history', [GuestServiceController::class, 'history'])->name('services.history');
        Route::get('/services/{service}', [GuestServiceController::class, 'show'])->name('services.show')
            ->where('service', '[0-9]+');
        
        // Service requests - SPECIFIC routes FIRST
        Route::get('/service-requests/{id}', [GuestServiceController::class, 'showRequest'])->name('service-requests.show');
        Route::post('/service-requests/{id}/cancel', [GuestServiceController::class, 'cancelRequest'])->name('service-requests.cancel');
        
        // DELETE routes - make sure these are properly defined
        Route::delete('/service-requests/{id}/delete', [GuestServiceController::class, 'deleteRequest'])->name('service-requests.delete');
        Route::delete('/service-requests/delete-cancelled', [GuestServiceController::class, 'deleteAllCancelled'])->name('service-requests.delete-cancelled');
    });
});
Route::prefix('admin')->name('admin.')->middleware(['auth', 'user.status', 'role:admin,manager,staff'])->group(function () {
    // ... existing admin routes ...
    
    // Payment routes - use PaymentController instead of AdminPaymentController
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::patch('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');
    Route::post('/payments/{payment}/refund', [PaymentController::class, 'processRefund'])->name('payments.refund');
    Route::get('/payments/{payment}/refund', [PaymentController::class, 'showRefundForm'])->name('payments.refund.form');
    Route::get('/payments-export', [PaymentController::class, 'export'])->name('payments.export'); // Note: moved export to avoid route conflicts
});

// Guest payment routes (additional routes that don't conflict)
Route::middleware(['auth'])->group(function () {
    Route::get('/payments/create/{booking}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/store/{booking}', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/confirmation/{payment}', [PaymentController::class, 'confirmation'])->name('payments.confirmation');
    Route::patch('/payments/{payment}/method', [PaymentController::class, 'updatePaymentMethod'])->name('payments.updateMethod');
});

// Admin, Manager, and Staff shared routes
Route::middleware(['auth', 'user.status', 'role:admin,manager,staff'])->group(function () {
    // Payment management routes (accessible by admin, manager, and staff)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::patch('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');
        Route::post('/payments/{payment}/refund', [PaymentController::class, 'processRefund'])->name('payments.refund');
        Route::get('/payments/{payment}/refund', [PaymentController::class, 'showRefundForm'])->name('payments.refund.form');
        Route::get('/payments-export', [PaymentController::class, 'export'])->name('payments.export');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });
});

// Admin-only routes (if any specific admin-only payment functions)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'user.status', 'role:admin'])->group(function () {
    // Add any admin-only payment routes here if needed
});

// Manager routes - add payment management access for manager, admin, and staff
Route::prefix('manager')->name('manager.')->middleware(['auth', 'user.status', 'role:manager,admin,staff'])->group(function () {
    // Existing manager routes...
    
    // Payment management routes for managers and staff
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::patch('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');
    Route::post('/payments/{payment}/refund', [PaymentController::class, 'processRefund'])->name('payments.refund');
    Route::get('/payments/{payment}/refund', [PaymentController::class, 'showRefundForm'])->name('payments.refund.form');
    Route::get('/payments-export', [PaymentController::class, 'export'])->name('payments.export');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
});

// Admin routes for service requests (using Manager controller for now)
Route::middleware(['auth', 'user.status', 'role:admin'])->group(function () {
    Route::get('/admin/service-requests/{serviceRequest}', [\App\Http\Controllers\Manager\ServiceRequestController::class, 'show'])
         ->name('admin.service-requests.show');
});

// Manager routes for service requests and payments - accessible to manager, admin, and staff
Route::middleware(['auth', 'user.status', 'role:manager,admin,staff'])->group(function () {
    Route::prefix('manager')->name('manager.')->group(function () {
        Route::get('/service-requests/{serviceRequest}', [\App\Http\Controllers\Manager\ServiceRequestController::class, 'show'])
             ->name('service-requests.show');
        Route::patch('/payments/{payment}/status', [\App\Http\Controllers\Manager\PaymentController::class, 'updateStatus'])
             ->name('payments.updateStatus');
        Route::post('/payments/{payment}/refund', [\App\Http\Controllers\Manager\PaymentController::class, 'processRefund'])
             ->name('payments.refund');
    });
});

// Reservation details for admin/manager/staff
Route::prefix('manager')->name('manager.')->middleware(['auth', 'user.status', 'role:admin,manager,staff'])->group(function () {
    // ...other manager routes...

    // Reservation details for admin/manager/staff
    Route::get('/reservations/{id}', [App\Http\Controllers\ManagerController::class, 'showReservation'])->name('reservations.show');
});

// Quick Book Room (show booking form for a specific room)
Route::get('/bookings/quick-book/{room}', [App\Http\Controllers\ManagerController::class, 'quickBookRoom'])->name('bookings.quick-book');

// DELETE ROUTE FOR GUEST BOOKINGS
Route::delete('/guest/bookings/{id}', [App\Http\Controllers\BookingController::class, 'destroy'])->name('guest.bookings.destroy');

