<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Temporary Admin Creation Route
|--------------------------------------------------------------------------
| This route is for emergency admin account creation only.
| DELETE THIS FILE after creating your admin account!
|
*/

Route::get('/create-admin-emergency', function () {
    try {
        // Create new admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@valesbeach.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active'
        ]);
        return response()->json([
            'message' => 'Admin account already exists!',
            'admin_email' => $existingAdmin->email
        ]);
    }

    // Create new admin user
    $admin = User::create([
        'name' => 'Emergency Admin',
        'email' => 'admin@valesbeach.com',
        'password' => Hash::make('admin123'),
        'role' => 'admin',
        'status' => 'active'
    ]);

    return response()->json([
        'message' => 'Emergency admin account created successfully!',
        'email' => 'admin@valesbeach.com',
        'password' => 'admin123',
        'note' => 'Please login and change the password immediately. Also delete the temp_admin.php file.'
    ]);
});
