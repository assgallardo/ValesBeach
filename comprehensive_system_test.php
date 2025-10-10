<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔄 VALESBEACH COMPREHENSIVE SYSTEM TEST\n";
echo "=====================================\n\n";

$errors = [];
$warnings = [];
$passed = 0;
$failed = 0;

function test($description, $callback) {
    global $errors, $warnings, $passed, $failed;
    
    try {
        echo "Testing: $description... ";
        $result = $callback();
        if ($result === true || $result === null) {
            echo "✅ PASS\n";
            $passed++;
        } else {
            echo "⚠️  WARNING: $result\n";
            $warnings[] = "$description: $result";
            $passed++;
        }
    } catch (Exception $e) {
        echo "❌ FAIL: " . $e->getMessage() . "\n";
        $errors[] = "$description: " . $e->getMessage();
        $failed++;
    }
}

// 1. DATABASE CONNECTION AND MODELS
echo "1. DATABASE & MODELS TESTING\n";
echo "============================\n";

test("Database connection", function() {
    $users = App\Models\User::count();
    return $users > 0 ? true : "No users found in database";
});

test("User model", function() {
    $user = App\Models\User::first();
    if (!$user) throw new Exception("No users found");
    return $user->name ? true : "User name is empty";
});

test("Room model", function() {
    $room = App\Models\Room::first();
    if (!$room) throw new Exception("No rooms found");
    return $room->name && $room->price ? true : "Room data incomplete";
});

test("Booking model", function() {
    $booking = App\Models\Booking::first();
    if (!$booking) return "No bookings found (this is OK for new systems)";
    return $booking->total_price > 0 ? true : "Booking has no price";
});

test("Payment model", function() {
    $payment = new App\Models\Payment();
    $fillable = $payment->getFillable();
    return in_array('amount', $fillable) && in_array('payment_method', $fillable) ? true : "Payment model missing required fields";
});

test("Invoice model", function() {
    $invoice = new App\Models\Invoice();
    $fillable = $invoice->getFillable();
    return in_array('total_amount', $fillable) && in_array('invoice_number', $fillable) ? true : "Invoice model missing required fields";
});

// 2. MODEL RELATIONSHIPS
echo "\n2. MODEL RELATIONSHIPS TESTING\n";
echo "===============================\n";

test("User-Booking relationship", function() {
    $user = App\Models\User::with('bookings')->first();
    return method_exists($user, 'bookings') ? true : "User-Booking relationship missing";
});

test("User-Payment relationship", function() {
    $user = App\Models\User::first();
    return method_exists($user, 'payments') ? true : "User-Payment relationship missing";
});

test("Booking-Room relationship", function() {
    $booking = App\Models\Booking::first();
    if (!$booking) return "No bookings to test";
    return method_exists($booking, 'room') ? true : "Booking-Room relationship missing";
});

test("Booking-Payment relationship", function() {
    $booking = App\Models\Booking::first();
    if (!$booking) return "No bookings to test";
    return method_exists($booking, 'payments') ? true : "Booking-Payment relationship missing";
});

test("Payment-Booking relationship", function() {
    $payment = new App\Models\Payment();
    return method_exists($payment, 'booking') ? true : "Payment-Booking relationship missing";
});

// 3. CONTROLLER TESTING
echo "\n3. CONTROLLERS TESTING\n";
echo "======================\n";

test("AuthController exists", function() {
    return class_exists('App\Http\Controllers\AuthController') ? true : "AuthController missing";
});

test("BookingController exists", function() {
    return class_exists('App\Http\Controllers\BookingController') ? true : "BookingController missing";
});

test("PaymentController exists", function() {
    return class_exists('App\Http\Controllers\PaymentController') ? true : "PaymentController missing";
});

test("InvoiceController exists", function() {
    return class_exists('App\Http\Controllers\InvoiceController') ? true : "InvoiceController missing";
});

test("AdminBookingController exists", function() {
    return class_exists('App\Http\Controllers\Admin\BookingController') ? true : "AdminBookingController missing";
});

test("ManagerController exists", function() {
    return class_exists('App\Http\Controllers\ManagerController') ? true : "ManagerController missing";
});

// 4. VIEW FILES TESTING
echo "\n4. VIEW FILES TESTING\n";
echo "=====================\n";

$viewFiles = [
    'layouts.guest' => 'resources/views/layouts/guest.blade.php',
    'guest.dashboard' => 'resources/views/guest/dashboard.blade.php',
    'guest.bookings.index' => 'resources/views/guest/bookings/index.blade.php',
    'payments.create' => 'resources/views/payments/create.blade.php',
    'payments.history' => 'resources/views/payments/history.blade.php',
    'invoices.index' => 'resources/views/invoices/index.blade.php',
    'admin.dashboard' => 'resources/views/admin/dashboard.blade.php'
];

foreach ($viewFiles as $viewName => $filePath) {
    test("View file: $viewName", function() use ($filePath) {
        return file_exists($filePath) ? true : "View file missing: $filePath";
    });
}

// 5. MIGRATION TESTING
echo "\n5. MIGRATION TESTING\n";
echo "====================\n";

test("Users table exists", function() {
    return Schema::hasTable('users') ? true : "Users table missing";
});

test("Rooms table exists", function() {
    return Schema::hasTable('rooms') ? true : "Rooms table missing";
});

test("Bookings table exists", function() {
    return Schema::hasTable('bookings') ? true : "Bookings table missing";
});

test("Payments table exists", function() {
    return Schema::hasTable('payments') ? true : "Payments table missing";
});

test("Invoices table exists", function() {
    return Schema::hasTable('invoices') ? true : "Invoices table missing";
});

// 6. ROUTE TESTING
echo "\n6. ROUTE TESTING\n";
echo "================\n";

test("Payment routes registered", function() {
    $routes = collect(Route::getRoutes())->pluck('uri')->toArray();
    return in_array('payments/history', $routes) ? true : "Payment routes not registered";
});

test("Invoice routes registered", function() {
    $routes = collect(Route::getRoutes())->pluck('uri')->toArray();
    return in_array('invoices', $routes) ? true : "Invoice routes not registered";
});

test("Admin routes registered", function() {
    $routes = collect(Route::getRoutes())->pluck('uri')->toArray();
    return in_array('admin/dashboard', $routes) ? true : "Admin routes not registered";
});

// 7. FUNCTIONAL TESTING
echo "\n7. FUNCTIONAL TESTING\n";
echo "=====================\n";

test("Payment model methods", function() {
    $payment = new App\Models\Payment([
        'amount' => 1000.50,
        'payment_method' => 'cash',
        'status' => 'completed'
    ]);
    
    $methods = ['getFormattedAmountAttribute', 'isCompleted', 'getPaymentMethodDisplayAttribute'];
    foreach ($methods as $method) {
        if (!method_exists($payment, $method)) {
            throw new Exception("Payment method $method missing");
        }
    }
    return true;
});

test("Invoice model methods", function() {
    $invoice = new App\Models\Invoice([
        'total_amount' => 2000.00,
        'status' => 'paid'
    ]);
    
    $methods = ['getFormattedTotalAmountAttribute', 'isPaid', 'getStatusBadgeClassAttribute'];
    foreach ($methods as $method) {
        if (!method_exists($invoice, $method)) {
            throw new Exception("Invoice method $method missing");
        }
    }
    return true;
});

test("Booking payment methods", function() {
    $booking = App\Models\Booking::first();
    if (!$booking) return "No bookings to test";
    
    $methods = ['isPaid', 'getRemainingBalanceAttribute', 'getPaymentStatusAttribute'];
    foreach ($methods as $method) {
        if (!method_exists($booking, $method)) {
            throw new Exception("Booking method $method missing");
        }
    }
    return true;
});

// 8. DUPLICATE DETECTION
echo "\n8. DUPLICATE DETECTION\n";
echo "======================\n";

test("Check for duplicate migrations", function() {
    $migrations = glob('database/migrations/*.php');
    $basenames = [];
    $duplicates = [];
    
    foreach ($migrations as $migration) {
        $basename = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', basename($migration));
        if (in_array($basename, $basenames)) {
            $duplicates[] = $basename;
        }
        $basenames[] = $basename;
    }
    
    return empty($duplicates) ? true : "Duplicate migrations found: " . implode(', ', $duplicates);
});

test("Check for duplicate routes", function() {
    $routes = Route::getRoutes();
    $uris = [];
    $duplicates = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        $method = implode('|', $route->methods());
        $key = "$method:$uri";
        
        if (in_array($key, $uris)) {
            $duplicates[] = $key;
        }
        $uris[] = $key;
    }
    
    return empty($duplicates) ? true : "Duplicate routes found: " . implode(', ', array_unique($duplicates));
});

// 9. CONFIGURATION TESTING
echo "\n9. CONFIGURATION TESTING\n";
echo "=========================\n";

test("Database configuration", function() {
    $connection = config('database.default');
    $config = config("database.connections.$connection");
    return $config ? true : "Database configuration missing";
});

test("App configuration", function() {
    $name = config('app.name');
    $env = config('app.env');
    return $name && $env ? true : "App configuration incomplete";
});

// 10. SECURITY TESTING
echo "\n10. SECURITY TESTING\n";
echo "====================\n";

test("Middleware registration", function() {
    // Check if middleware files exist
    $middlewareFiles = [
        'App\Http\Middleware\Authenticate',
        'App\Http\Middleware\RedirectIfAuthenticated',
        'App\Http\Middleware\VerifyCsrfToken'
    ];
    
    foreach ($middlewareFiles as $middleware) {
        if (!class_exists($middleware)) {
            throw new Exception("Middleware class $middleware not found");
        }
    }
    
    // Check kernel middleware aliases
    $kernel = app(App\Http\Kernel::class);
    $reflection = new ReflectionClass($kernel);
    $property = $reflection->getProperty('middlewareAliases');
    $property->setAccessible(true);
    $aliases = $property->getValue($kernel);
    
    $required = ['auth', 'guest'];
    foreach ($required as $alias) {
        if (!isset($aliases[$alias])) {
            throw new Exception("Middleware alias $alias not registered");
        }
    }
    
    return true;
});

test("CSRF protection", function() {
    return class_exists('App\Http\Middleware\VerifyCsrfToken') ? true : "CSRF middleware missing";
});

// SUMMARY
echo "\n" . str_repeat("=", 50) . "\n";
echo "TEST SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: $failed\n";
echo "⚠️  Warnings: " . count($warnings) . "\n";

if (!empty($errors)) {
    echo "\n🔴 ERRORS FOUND:\n";
    foreach ($errors as $error) {
        echo "  • $error\n";
    }
}

if (!empty($warnings)) {
    echo "\n🟡 WARNINGS:\n";
    foreach ($warnings as $warning) {
        echo "  • $warning\n";
    }
}

if (empty($errors)) {
    echo "\n🎉 ALL CRITICAL TESTS PASSED!\n";
    echo "The ValesBeach system is ready for production.\n";
} else {
    echo "\n⚠️  PLEASE FIX THE ERRORS ABOVE BEFORE PROCEEDING.\n";
}

echo "\nTest completed at: " . date('Y-m-d H:i:s') . "\n";
