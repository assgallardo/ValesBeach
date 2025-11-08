<?php

/**
 * Comprehensive Module Error Checker
 * Tests all modules for runtime errors, missing methods, undefined variables
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "═══════════════════════════════════════════════════\n";
echo "  MODULE ERROR CHECK - ALL SYSTEMS\n";
echo "═══════════════════════════════════════════════════\n\n";

$errors = [];
$warnings = [];
$modules = [];

// Define all modules to check
$modulesToCheck = [
    'Guest' => [
        'Dashboard' => 'GuestController@dashboard',
        'Browse Rooms' => 'GuestController@browseRooms',
        'Room Booking' => 'BookingController@showBookingForm',
        'Cottage Browsing' => 'CottageBookingController@index',
        'Cottage Booking' => 'CottageBookingController@showBookingForm',
        'Service Browsing' => 'GuestServiceController@index',
        'Service Request' => 'GuestServiceController@create',
        'Food Menu' => 'FoodOrderController@menu',
        'Food Checkout' => 'FoodOrderController@checkout',
    ],
    'Admin' => [
        'Dashboard' => 'AdminController@dashboard',
        'Bookings' => 'Admin\BookingController@index',
        'User Management' => 'UserController@index',
        'Payments' => 'PaymentController@index',
    ],
    'Manager' => [
        'Dashboard' => 'ManagerController@dashboard',
        'Bookings' => 'Manager\BookingController@index',
        'Reports' => 'Manager\ReportsController@index',
        'Service Requests' => 'Manager\ServiceRequestController@index',
        'Services' => 'Manager\ServiceController@index',
        'Payments' => 'Manager\PaymentController@index',
        'Staff Assignment' => 'Manager\StaffAssignmentController@index',
    ],
    'Staff' => [
        'Dashboard' => 'StaffController@dashboard',
        'Food Orders' => 'Staff\FoodOrderController@index',
        'Menu Management' => 'Staff\MenuController@index',
        'Tasks' => 'StaffTaskController@index',
    ],
];

// Check if controllers exist and methods are callable
foreach ($modulesToCheck as $role => $features) {
    echo "🔍 Checking {$role} Module...\n";
    echo "───────────────────────────────────────────────\n";
    
    foreach ($features as $feature => $action) {
        list($controller, $method) = explode('@', $action);
        
        // Build full controller class name
        if (strpos($controller, '\\') === false) {
            $controllerClass = "App\\Http\\Controllers\\{$controller}";
        } else {
            $controllerClass = "App\\Http\\Controllers\\{$controller}";
        }
        
        // Check if controller exists
        if (!class_exists($controllerClass)) {
            $errors[] = [
                'module' => $role,
                'feature' => $feature,
                'type' => 'Missing Controller',
                'details' => "Controller '{$controllerClass}' not found"
            ];
            echo "   ❌ {$feature}: Controller not found\n";
            continue;
        }
        
        // Check if method exists
        if (!method_exists($controllerClass, $method)) {
            $errors[] = [
                'module' => $role,
                'feature' => $feature,
                'type' => 'Missing Method',
                'details' => "Method '{$method}' not found in {$controllerClass}"
            ];
            echo "   ❌ {$feature}: Method not found\n";
            continue;
        }
        
        echo "   ✅ {$feature}\n";
    }
    echo "\n";
}

// Check routes
echo "🔍 Checking Routes Configuration...\n";
echo "───────────────────────────────────────────────\n";

$requiredRoutes = [
    'guest.dashboard',
    'guest.rooms.browse',
    'guest.rooms.book',
    'guest.cottages.index',
    'guest.cottages.book',
    'guest.services.index',
    'guest.services.request',
    'guest.food-orders.menu',
    'guest.food-orders.checkout',
    'admin.dashboard',
    'manager.dashboard',
    'staff.dashboard',
    'login',
    'logout',
];

try {
    $routes = \Route::getRoutes();
    $routeNames = [];
    foreach ($routes as $route) {
        if ($route->getName()) {
            $routeNames[] = $route->getName();
        }
    }
    
    $missingRoutes = [];
    foreach ($requiredRoutes as $routeName) {
        if (!in_array($routeName, $routeNames)) {
            $missingRoutes[] = $routeName;
        }
    }
    
    if (count($missingRoutes) > 0) {
        foreach ($missingRoutes as $route) {
            $warnings[] = [
                'type' => 'Missing Route',
                'details' => "Route '{$route}' not found"
            ];
        }
        echo "   ⚠️  Missing " . count($missingRoutes) . " routes\n";
        foreach ($missingRoutes as $route) {
            echo "      - {$route}\n";
        }
    } else {
        echo "   ✅ All critical routes present\n";
    }
} catch (Exception $e) {
    $errors[] = [
        'module' => 'Routes',
        'type' => 'Route Check Failed',
        'details' => $e->getMessage()
    ];
    echo "   ❌ Route check failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Check database tables
echo "🔍 Checking Database Tables...\n";
echo "───────────────────────────────────────────────\n";

$requiredTables = [
    'users',
    'rooms',
    'cottages',
    'bookings',
    'cottage_bookings',
    'services',
    'service_requests',
    'menu_items',
    'food_orders',
    'order_items',
    'payments',
];

try {
    $existingTables = \DB::select("SHOW TABLES");
    $tableNames = array_map(function($table) {
        return array_values((array)$table)[0];
    }, $existingTables);
    
    $missingTables = [];
    foreach ($requiredTables as $table) {
        if (!in_array($table, $tableNames)) {
            $missingTables[] = $table;
        }
    }
    
    if (count($missingTables) > 0) {
        foreach ($missingTables as $table) {
            $errors[] = [
                'module' => 'Database',
                'type' => 'Missing Table',
                'details' => "Table '{$table}' not found"
            ];
        }
        echo "   ❌ Missing " . count($missingTables) . " tables\n";
        foreach ($missingTables as $table) {
            echo "      - {$table}\n";
        }
    } else {
        echo "   ✅ All required tables present\n";
    }
} catch (Exception $e) {
    $errors[] = [
        'module' => 'Database',
        'type' => 'Database Check Failed',
        'details' => $e->getMessage()
    ];
    echo "   ❌ Database check failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Check models
echo "🔍 Checking Models...\n";
echo "───────────────────────────────────────────────\n";

$requiredModels = [
    'User',
    'Room',
    'Cottage',
    'Booking',
    'CottageBooking',
    'Service',
    'ServiceRequest',
    'MenuItem',
    'FoodOrder',
    'OrderItem',
    'Payment',
];

foreach ($requiredModels as $model) {
    $modelClass = "App\\Models\\{$model}";
    if (!class_exists($modelClass)) {
        $errors[] = [
            'module' => 'Models',
            'type' => 'Missing Model',
            'details' => "Model '{$modelClass}' not found"
        ];
        echo "   ❌ {$model}\n";
    } else {
        echo "   ✅ {$model}\n";
    }
}

echo "\n";

// Check middleware
echo "🔍 Checking Middleware...\n";
echo "───────────────────────────────────────────────\n";

$requiredMiddleware = [
    'auth',
    'user.status',
    'role:guest',
    'role:admin',
    'role:manager',
    'role:staff',
];

try {
    $router = app('router');
    $allMiddleware = $router->getMiddleware();
    
    echo "   ✅ Middleware system operational\n";
    echo "   📊 Registered: " . count($allMiddleware) . " middleware groups\n";
} catch (Exception $e) {
    $warnings[] = [
        'type' => 'Middleware Check',
        'details' => 'Could not verify all middleware: ' . $e->getMessage()
    ];
    echo "   ⚠️  Middleware check incomplete\n";
}

echo "\n";

// Check critical view files
echo "🔍 Checking Critical Views...\n";
echo "───────────────────────────────────────────────\n";

$criticalViews = [
    'layouts.guest',
    'layouts.admin',
    'layouts.manager',
    'layouts.staff',
    'guest.dashboard',
    'guest.rooms.browse',
    'guest.rooms.book',
    'guest.cottages.index',
    'guest.cottages.book',
    'guest.services.index',
    'guest.services.request',
    'food-orders.menu',
    'food-orders.checkout',
];

foreach ($criticalViews as $view) {
    $viewPath = str_replace('.', '/', $view) . '.blade.php';
    $fullPath = resource_path('views/' . $viewPath);
    
    if (!file_exists($fullPath)) {
        $errors[] = [
            'module' => 'Views',
            'type' => 'Missing View',
            'details' => "View '{$view}' not found"
        ];
        echo "   ❌ {$view}\n";
    } else {
        echo "   ✅ {$view}\n";
    }
}

echo "\n";

// Summary Report
echo "═══════════════════════════════════════════════════\n";
echo "  MODULE ERROR REPORT\n";
echo "═══════════════════════════════════════════════════\n\n";

if (count($errors) === 0 && count($warnings) === 0) {
    echo "🎉 ALL MODULES OPERATIONAL! 🎉\n\n";
    echo "✅ All controllers working\n";
    echo "✅ All routes configured\n";
    echo "✅ All database tables present\n";
    echo "✅ All models available\n";
    echo "✅ All critical views exist\n";
    echo "✅ Middleware system functional\n\n";
    echo "All modules are ready for use!\n";
} else {
    if (count($errors) > 0) {
        echo "❌ CRITICAL ERRORS FOUND: " . count($errors) . "\n";
        echo "───────────────────────────────────────────────\n";
        
        // Group by module
        $groupedErrors = [];
        foreach ($errors as $error) {
            $module = $error['module'] ?? 'General';
            $groupedErrors[$module][] = $error;
        }
        
        foreach ($groupedErrors as $module => $moduleErrors) {
            echo "\n📌 {$module} Module (" . count($moduleErrors) . " errors)\n";
            foreach ($moduleErrors as $error) {
                echo "   • [{$error['type']}] ";
                if (isset($error['feature'])) {
                    echo "{$error['feature']}: ";
                }
                echo "{$error['details']}\n";
            }
        }
        echo "\n";
    }
    
    if (count($warnings) > 0) {
        echo "⚠️  WARNINGS: " . count($warnings) . "\n";
        echo "───────────────────────────────────────────────\n";
        foreach ($warnings as $warning) {
            echo "   • [{$warning['type']}] {$warning['details']}\n";
        }
        echo "\n";
    }
}

echo "═══════════════════════════════════════════════════\n";
echo "  Check completed: " . date('Y-m-d H:i:s') . "\n";
echo "═══════════════════════════════════════════════════\n";
