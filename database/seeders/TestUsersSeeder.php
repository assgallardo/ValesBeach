<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing users (optional - comment out if you want to keep existing users)
        // User::truncate();

        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@valesbeach.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Manager User
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@valesbeach.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Staff User
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@valesbeach.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Guest User
        User::create([
            'name' => 'Guest User',
            'email' => 'guest@valesbeach.com',
            'password' => Hash::make('guest123'),
            'role' => 'guest',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Test users created successfully!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@valesbeach.com', 'admin123'],
                ['Manager', 'manager@valesbeach.com', 'manager123'],
                ['Staff', 'staff@valesbeach.com', 'staff123'],
                ['Guest', 'guest@valesbeach.com', 'guest123'],
            ]
        );
    }
}
