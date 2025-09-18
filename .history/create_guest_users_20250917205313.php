<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Creating Guest Users\n";
echo "===================\n";

// Create some guest users for testing
$guestUsers = [
    [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'guest',
        'status' => 'active'
    ],
    [
        'name' => 'Jane Smith', 
        'email' => 'jane@example.com',
        'role' => 'guest',
        'status' => 'active'
    ],
    [
        'name' => 'Bob Johnson',
        'email' => 'bob@example.com', 
        'role' => 'guest',
        'status' => 'active'
    ]
];

foreach ($guestUsers as $userData) {
    $user = User::updateOrCreate(
        ['email' => $userData['email']],
        array_merge($userData, ['password' => Hash::make('password123')])
    );
    echo "Created/Updated guest user: {$user->name} ({$user->email})\n";
}

echo "\nGuest users created. Now running BookingSeeder...\n";

// Run the booking seeder manually
$seeder = new \Database\Seeders\BookingSeeder();
$seeder->run();

echo "BookingSeeder completed!\n";
