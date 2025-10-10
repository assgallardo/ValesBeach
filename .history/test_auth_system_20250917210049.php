<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Testing Authentication System\n";
echo "============================\n";

// Test 1: Verify all user roles exist
$roles = ['admin', 'manager', 'staff', 'guest'];
foreach ($roles as $role) {
    $count = User::where('role', $role)->count();
    echo "Users with role '{$role}': {$count}\n";
}

echo "\n";

// Test 2: Verify passwords can be checked
$adminUser = User::where('email', 'admin@valesbeach.com')->first();
if ($adminUser) {
    $passwordCheck = Hash::check('admin123', $adminUser->password);
    echo "Admin password check: " . ($passwordCheck ? 'PASS' : 'FAIL') . "\n";
} else {
    echo "Admin user not found!\n";
}

$guestUser = User::where('role', 'guest')->first();
if ($guestUser) {
    $passwordCheck = Hash::check('password123', $guestUser->password);
    echo "Guest password check: " . ($passwordCheck ? 'PASS' : 'FAIL') . "\n";
} else {
    echo "Guest user not found!\n";
}

echo "\n";

// Test 3: Check if all required authentication fields exist
echo "Checking user model structure:\n";
$user = User::first();
if ($user) {
    $fields = ['id', 'name', 'email', 'password', 'role', 'status'];
    foreach ($fields as $field) {
        if (isset($user->{$field})) {
            echo "✓ Field '{$field}' exists\n";
        } else {
            echo "✗ Field '{$field}' missing\n";
        }
    }
}

echo "\nAuthentication system test completed!\n";
