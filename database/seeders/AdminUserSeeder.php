<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@valesbeach.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        echo "Admin account created successfully!\n";
        echo "Email: admin@valesbeach.com\n";
        echo "Password: admin123\n";
        echo "Please change the password after first login.\n";
    }
}
