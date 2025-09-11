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
        
        $this->command->info('Admin user created/updated successfully.');
    }
}
