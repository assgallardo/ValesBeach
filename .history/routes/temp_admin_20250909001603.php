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
        // Force delete any existing admin accounts
        User::where('role', 'admin')->delete();

        // Create new admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@valesbeach.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        return "Admin account created successfully!<br><br>"
            . "Email: admin@valesbeach.com<br>"
            . "Password: admin123<br><br>"
            . "Please change your password after logging in.<br>"
            . "Remember to delete the temp_admin.php file for security!";
    } catch (\Exception $e) {
        return "Error creating admin account: " . $e->getMessage();
    }
});
