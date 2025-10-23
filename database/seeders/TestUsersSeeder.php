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
        // Use updateOrCreate to make this idempotent (can run multiple times)
        
        // Create or Update Admin User
        User::updateOrCreate(
            ['email' => 'admin@valesbeach.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create or Update Manager User
        User::updateOrCreate(
            ['email' => 'manager@valesbeach.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create or Update Staff User
        User::updateOrCreate(
            ['email' => 'staff@valesbeach.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create or Update Guest User
        User::updateOrCreate(
            ['email' => 'guest@valesbeach.com'],
            [
                'name' => 'Guest User',
                'password' => Hash::make('guest123'),
                'role' => 'guest',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✓ Test users created/updated successfully!');
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
