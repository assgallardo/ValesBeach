<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Force create new admin user
        User::updateOrCreate(
            ['email' => 'admin@valesbeach.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active'
            ]
        );
        
        // Create manager user
        User::updateOrCreate(
            ['email' => 'manager@valesbeach.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'status' => 'active'
            ]
        );
        
        // Create staff user
        User::updateOrCreate(
            ['email' => 'staff@valesbeach.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'status' => 'active'
            ]
        );
        
        $this->command->info('Admin, Manager, and Staff users created/updated successfully.');
    }
}
