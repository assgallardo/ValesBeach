<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌊 Seeding ValesBeach Resort Database...');
        
        // Seed in order of dependencies
        $this->call([
            // 1. Users (use TestUsersSeeder which creates all test users)
            TestUsersSeeder::class,
            
            // 2. Rooms and rates
            RoomSeeder::class,
            ValesBeachRatesSeeder::class,
            
            // 3. Services
            ServicesSeeder::class,
            
            // 4. Food menu
            MenuCategorySeeder::class,
            MenuItemSeeder::class,
            
            // 5. Sample bookings (optional - comment out if not needed)
            // BookingsSeeder::class,
        ]);
        
        $this->command->info('✓ Database seeded successfully!');
        $this->command->info('📧 Test Users:');
        $this->command->info('   - admin@valesbeach.com / admin123');
        $this->command->info('   - manager@valesbeach.com / manager123');
        $this->command->info('   - staff@valesbeach.com / staff123');
        $this->command->info('   - guest@valesbeach.com / guest123');
    }
}
