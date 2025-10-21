<?php

/**
 * Route Testing Script
 * Tests all routes and their availability
 * 
 * Run with: php artisan test:routes
 */

namespace Tests;

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║              ROUTE VERIFICATION TEST                   ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

// Get all registered routes
$routes = Route::getRoutes();
echo "📊 Total Routes Registered: " . count($routes) . "\n\n";

// Test existing routes
echo "🔍 EXISTING ROUTES VERIFICATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$routeTests = [
    // Guest routes
    'guest.dashboard' => 'GET',
    'guest.bookings' => 'GET',
    'guest.rooms.browse' => 'GET',
    'guest.services.index' => 'GET',
    
    // Payment routes
    'payments.create' => 'GET',
    'payments.store' => 'POST',
    'payments.history' => 'GET',
    'payments.show' => 'GET',
    'payments.confirmation' => 'GET',
    
    // Admin routes
    'admin.dashboard' => 'GET',
    'admin.bookings.index' => 'GET',
    'admin.payments.index' => 'GET',
    
    // Manager routes
    'manager.dashboard' => 'GET',
    
    // Invoice routes (restricted)
    'invoices.index' => 'GET',
    'invoices.show' => 'GET',
];

foreach ($routeTests as $routeName => $method) {
    if (Route::has($routeName)) {
        $route = Route::getRoutes()->getByName($routeName);
        $methods = $route->methods();
        echo "✅ Route '$routeName' exists (" . implode('|', $methods) . ")\n";
        echo "   URI: " . $route->uri() . "\n";
        $middleware = $route->middleware();
        if (!empty($middleware)) {
            echo "   Middleware: " . implode(', ', $middleware) . "\n";
        }
    } else {
        echo "❌ Route '$routeName' NOT FOUND\n";
    }
}

echo "\n";

// Test route groups
echo "🔍 ROUTE GROUPS VERIFICATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$routeGroups = [
    'guest' => [],
    'admin' => [],
    'manager' => [],
    'payments' => [],
    'invoices' => [],
];

foreach ($routes as $route) {
    $name = $route->getName();
    if ($name) {
        foreach ($routeGroups as $group => &$routeList) {
            if (str_starts_with($name, $group . '.')) {
                $routeList[] = $name;
            }
        }
    }
}

foreach ($routeGroups as $group => $routeList) {
    echo "📁 Group '$group': " . count($routeList) . " routes\n";
}

echo "\n";

// Check for middleware
echo "🔍 MIDDLEWARE VERIFICATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$middlewareCount = [
    'auth' => 0,
    'guest' => 0,
    'role:admin' => 0,
    'role:manager' => 0,
    'role:staff' => 0,
];

foreach ($routes as $route) {
    $middleware = $route->middleware();
    foreach ($middlewareCount as $mw => &$count) {
        if (in_array($mw, $middleware) || str_contains(implode(',', $middleware), $mw)) {
            $count++;
        }
    }
}

foreach ($middlewareCount as $middleware => $count) {
    echo "🔒 Middleware '$middleware': $count routes\n";
}

echo "\n";

// Routes by HTTP method
echo "🔍 ROUTES BY HTTP METHOD\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$methodCount = [
    'GET' => 0,
    'POST' => 0,
    'PUT' => 0,
    'PATCH' => 0,
    'DELETE' => 0,
];

foreach ($routes as $route) {
    foreach ($route->methods() as $method) {
        if (isset($methodCount[$method])) {
            $methodCount[$method]++;
        }
    }
}

foreach ($methodCount as $method => $count) {
    echo "📍 $method: $count routes\n";
}

echo "\n";

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║              ROUTE TEST COMPLETE                       ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";
