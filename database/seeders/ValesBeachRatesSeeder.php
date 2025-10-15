<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValesBeachRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data (use delete instead of truncate due to foreign keys)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('services')->truncate();
        DB::table('rooms')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ========================================
        // ROOMS / COTTAGES
        // ========================================
        
        $rooms = [
            // Umbrella Type Cottages (10 units)
            ['name' => 'Umbrella Cottage 1', 'type' => 'Umbrella Cottage', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱350.00 | Night Rate (6:00 PM – 6:00 AM): ₱400.00 + ₱5.00/head', 'capacity' => 25, 'beds' => 0, 'price' => 350.00, 'amenities' => json_encode(['Outdoor seating', '25 pax capacity']), 'status' => 'available'],
            ['name' => 'Umbrella Cottage 2', 'type' => 'Umbrella Cottage', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱350.00 | Night Rate (6:00 PM – 6:00 AM): ₱400.00 + ₱5.00/head', 'capacity' => 25, 'beds' => 0, 'price' => 350.00, 'amenities' => json_encode(['Outdoor seating', '25 pax capacity']), 'status' => 'available'],
            ['name' => 'Umbrella Cottage 3', 'type' => 'Umbrella Cottage', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱350.00 | Night Rate (6:00 PM – 6:00 AM): ₱400.00 + ₱5.00/head', 'capacity' => 25, 'beds' => 0, 'price' => 350.00, 'amenities' => json_encode(['Outdoor seating', '25 pax capacity']), 'status' => 'available'],
            ['name' => 'Umbrella Cottage 4', 'type' => 'Umbrella Cottage', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱350.00 | Night Rate (6:00 PM – 6:00 AM): ₱400.00 + ₱5.00/head', 'capacity' => 25, 'beds' => 0, 'price' => 350.00, 'amenities' => json_encode(['Outdoor seating', '25 pax capacity']), 'status' => 'available'],
            ['name' => 'Umbrella Cottage 5', 'type' => 'Umbrella Cottage', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱350.00 | Night Rate (6:00 PM – 6:00 AM): ₱400.00 + ₱5.00/head', 'capacity' => 25, 'beds' => 0, 'price' => 350.00, 'amenities' => json_encode(['Outdoor seating', '25 pax capacity']), 'status' => 'available'],
            ['name' => 'Umbrella Cottage 6', 'type' => 'Umbrella Cottage', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱350.00 | Night Rate (6:00 PM – 6:00 AM): ₱400.00 + ₱5.00/head', 'capacity' => 25, 'beds' => 0, 'price' => 350.00, 'amenities' => json_encode(['Outdoor seating', '25 pax capacity']), 'status' => 'available'],
            ['name' => 'Umbrella Cottage 7', 'type' => 'Umbrella Cottage', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱350.00 | Night Rate (6:00 PM – 6:00 AM): ₱400.00 + ₱5.00/head', 'capacity' => 25, 'beds' => 0, 'price' => 350.00, 'amenities' => json_encode(['Outdoor seating', '25 pax capacity']), 'status' => 'available'],
            ['name' => 'Umbrella Cottage 8', 'type' => 'Umbrella Cottage', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱350.00 | Night Rate (6:00 PM – 6:00 AM): ₱400.00 + ₱5.00/head', 'capacity' => 25, 'beds' => 0, 'price' => 350.00, 'amenities' => json_encode(['Outdoor seating', '25 pax capacity']), 'status' => 'available'],
            ['name' => 'Umbrella Cottage 9', 'type' => 'Umbrella Cottage', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱350.00 | Night Rate (6:00 PM – 6:00 AM): ₱400.00 + ₱5.00/head', 'capacity' => 25, 'beds' => 0, 'price' => 350.00, 'amenities' => json_encode(['Outdoor seating', '25 pax capacity']), 'status' => 'available'],
            ['name' => 'Umbrella Cottage 10', 'type' => 'Umbrella Cottage', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱350.00 | Night Rate (6:00 PM – 6:00 AM): ₱400.00 + ₱5.00/head', 'capacity' => 25, 'beds' => 0, 'price' => 350.00, 'amenities' => json_encode(['Outdoor seating', '25 pax capacity']), 'status' => 'available'],
            
            // Bahay Kubo Type Cottages (8 units)
            ['name' => 'Bahay Kubo 1', 'type' => 'Bahay Kubo', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱200.00 | Night Rate (6:00 PM – 6:00 AM): ₱250.00 + ₱5.00/head', 'capacity' => 20, 'beds' => 0, 'price' => 200.00, 'amenities' => json_encode(['Traditional Filipino cottage', '20 pax capacity']), 'status' => 'available'],
            ['name' => 'Bahay Kubo 2', 'type' => 'Bahay Kubo', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱200.00 | Night Rate (6:00 PM – 6:00 AM): ₱250.00 + ₱5.00/head', 'capacity' => 20, 'beds' => 0, 'price' => 200.00, 'amenities' => json_encode(['Traditional Filipino cottage', '20 pax capacity']), 'status' => 'available'],
            ['name' => 'Bahay Kubo 3', 'type' => 'Bahay Kubo', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱200.00 | Night Rate (6:00 PM – 6:00 AM): ₱250.00 + ₱5.00/head', 'capacity' => 20, 'beds' => 0, 'price' => 200.00, 'amenities' => json_encode(['Traditional Filipino cottage', '20 pax capacity']), 'status' => 'available'],
            ['name' => 'Bahay Kubo 4', 'type' => 'Bahay Kubo', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱200.00 | Night Rate (6:00 PM – 6:00 AM): ₱250.00 + ₱5.00/head', 'capacity' => 20, 'beds' => 0, 'price' => 200.00, 'amenities' => json_encode(['Traditional Filipino cottage', '20 pax capacity']), 'status' => 'available'],
            ['name' => 'Bahay Kubo 5', 'type' => 'Bahay Kubo', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱200.00 | Night Rate (6:00 PM – 6:00 AM): ₱250.00 + ₱5.00/head', 'capacity' => 20, 'beds' => 0, 'price' => 200.00, 'amenities' => json_encode(['Traditional Filipino cottage', '20 pax capacity']), 'status' => 'available'],
            ['name' => 'Bahay Kubo 6', 'type' => 'Bahay Kubo', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱200.00 | Night Rate (6:00 PM – 6:00 AM): ₱250.00 + ₱5.00/head', 'capacity' => 20, 'beds' => 0, 'price' => 200.00, 'amenities' => json_encode(['Traditional Filipino cottage', '20 pax capacity']), 'status' => 'available'],
            ['name' => 'Bahay Kubo 7', 'type' => 'Bahay Kubo', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱200.00 | Night Rate (6:00 PM – 6:00 AM): ₱250.00 + ₱5.00/head', 'capacity' => 20, 'beds' => 0, 'price' => 200.00, 'amenities' => json_encode(['Traditional Filipino cottage', '20 pax capacity']), 'status' => 'available'],
            ['name' => 'Bahay Kubo 8', 'type' => 'Bahay Kubo', 'description' => 'Day Rate (6:00 AM – 6:00 PM): ₱200.00 | Night Rate (6:00 PM – 6:00 AM): ₱250.00 + ₱5.00/head', 'capacity' => 20, 'beds' => 0, 'price' => 200.00, 'amenities' => json_encode(['Traditional Filipino cottage', '20 pax capacity']), 'status' => 'available'],
            
            // Beer Garden
            ['name' => 'Beer Garden', 'type' => 'Beer Garden', 'description' => 'Day Rate: ₱2,000.00 | Night Rate: ₱2,500.00 + ₱5.00/head. Inclusions: 4 long tables and 25 chairs', 'capacity' => 50, 'beds' => 0, 'price' => 2000.00, 'amenities' => json_encode(['4 long tables', '25 chairs', '50 pax capacity']), 'status' => 'available'],
            
            // Dining Hall
            ['name' => 'Dining Hall', 'type' => 'Dining Hall', 'description' => 'Day Rate: ₱4,000.00 | Night Rate: ₱4,500.00', 'capacity' => 50, 'beds' => 0, 'price' => 4000.00, 'amenities' => json_encode(['Large dining area', '50 pax capacity']), 'status' => 'available'],
            
            // Function Halls
            ['name' => 'Function Hall (Airconditioned)', 'type' => 'Function Hall AC', 'description' => '₱5,000.00 for first 4 hours, ₱500.00 succeeding hours, ₱30.00 per excess person. Includes: 22 tables with white seat covers, 22 tables with white tablecloths, Sound system and microphone', 'capacity' => 100, 'beds' => 0, 'price' => 5000.00, 'amenities' => json_encode(['Air-conditioned', '22 tables with seat covers', '22 tablecloths', 'Sound system', 'Microphone', '100 pax capacity']), 'status' => 'available'],
            ['name' => 'Function Hall (Non-Airconditioned)', 'type' => 'Function Hall Non-AC', 'description' => '₱4,500.00 for first 4 hours, ₱450.00 succeeding hours, ₱20.00 per excess person. Includes: 22 tables with white seat covers, 22 tables with white tablecloths, Sound system and microphone', 'capacity' => 100, 'beds' => 0, 'price' => 4500.00, 'amenities' => json_encode(['22 tables with seat covers', '22 tablecloths', 'Sound system', 'Microphone', '100 pax capacity']), 'status' => 'available'],
            
            // Standard Rooms (6 units total)
            ['name' => 'Room 101', 'type' => 'Standard Room', 'description' => 'Good for 2 persons. Includes Free WiFi and TV. Check-out time: 12 Noon. Extra Bed: ₱350.00', 'capacity' => 2, 'beds' => 2, 'price' => 1000.00, 'amenities' => json_encode(['Free WiFi', 'TV', '2 Single Beds']), 'status' => 'available'],
            ['name' => 'Room 102', 'type' => 'Standard Room', 'description' => 'Good for 2 persons. Includes Free WiFi and TV. Check-out time: 12 Noon. Extra Bed: ₱350.00', 'capacity' => 2, 'beds' => 2, 'price' => 1000.00, 'amenities' => json_encode(['Free WiFi', 'TV', '2 Single Beds']), 'status' => 'available'],
            ['name' => 'Room 103', 'type' => 'Standard Room', 'description' => 'Good for 2 persons. Includes Free WiFi and TV. Check-out time: 12 Noon. Extra Bed: ₱350.00', 'capacity' => 2, 'beds' => 2, 'price' => 1000.00, 'amenities' => json_encode(['Free WiFi', 'TV', '2 Single Beds']), 'status' => 'available'],
            ['name' => 'Room 201', 'type' => 'Matrimonial Room', 'description' => 'Good for 2 persons. Includes Free WiFi and TV. Check-out time: 12 Noon. Extra Bed: ₱350.00', 'capacity' => 2, 'beds' => 1, 'price' => 1000.00, 'amenities' => json_encode(['Free WiFi', 'TV', 'Matrimonial Bed']), 'status' => 'available'],
            ['name' => 'Room 202', 'type' => 'Matrimonial Room', 'description' => 'Good for 2 persons. Includes Free WiFi and TV. Check-out time: 12 Noon. Extra Bed: ₱350.00', 'capacity' => 2, 'beds' => 1, 'price' => 1000.00, 'amenities' => json_encode(['Free WiFi', 'TV', 'Matrimonial Bed']), 'status' => 'available'],
            ['name' => 'Room 203', 'type' => 'Matrimonial Room', 'description' => 'Good for 2 persons. Includes Free WiFi and TV. Check-out time: 12 Noon. Extra Bed: ₱350.00', 'capacity' => 2, 'beds' => 1, 'price' => 1000.00, 'amenities' => json_encode(['Free WiFi', 'TV', 'Matrimonial Bed']), 'status' => 'available'],
            
            // Executive Cottage
            ['name' => 'Executive Cottage', 'type' => 'Executive', 'description' => '3 Airconditioned Rooms with Hot and Cold Shower, Mini Kitchen and Bar, Living Room with TV', 'capacity' => 6, 'beds' => 3, 'price' => 7500.00, 'amenities' => json_encode(['3 Airconditioned Rooms', 'Hot and Cold Shower', 'Mini Kitchen', 'Bar', 'Living Room', 'TV']), 'status' => 'available'],
        ];

        foreach ($rooms as $room) {
            DB::table('rooms')->insert([
                'name' => $room['name'],
                'type' => $room['type'],
                'description' => $room['description'],
                'capacity' => $room['capacity'],
                'beds' => $room['beds'],
                'price' => $room['price'],
                'amenities' => $room['amenities'],
                'status' => $room['status'],
                'is_available' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ========================================
        // SERVICES
        // ========================================
        
        $services = [
            // Room Services
            [
                'name' => 'Extra Bed',
                'description' => 'Additional bed for rooms',
                'category' => 'room_service',
                'price' => 350.00,
                'duration' => null,
                'capacity' => 1,
                'is_available' => 1,
            ],
            
            // Activities - Cottage Add-ons
            [
                'name' => 'Excess Pax (Daytime)',
                'description' => 'Additional person charge for cottages during daytime (6:00 AM - 6:00 PM)',
                'category' => 'activities',
                'price' => 10.00,
                'duration' => null,
                'capacity' => 1,
                'is_available' => 1,
            ],
            [
                'name' => 'Excess Pax (Nighttime)',
                'description' => 'Additional person charge for cottages during nighttime (6:00 PM - 6:00 AM)',
                'category' => 'activities',
                'price' => 15.00,
                'duration' => null,
                'capacity' => 1,
                'is_available' => 1,
            ],
            [
                'name' => 'Night Rate Additional Fee per Head',
                'description' => 'Additional charge per person for night bookings in cottages',
                'category' => 'activities',
                'price' => 5.00,
                'duration' => null,
                'capacity' => 1,
                'is_available' => 1,
            ],
            
            // Room Service - Table Rental
            [
                'name' => 'Table Rental with 6 Chairs',
                'description' => 'Table rental includes 6 chairs',
                'category' => 'room_service',
                'price' => 150.00,
                'duration' => null,
                'capacity' => 6,
                'is_available' => 1,
            ],
            
            // Room Service - Function Hall Add-ons
            [
                'name' => 'Function Hall - Succeeding Hour (AC)',
                'description' => 'Additional hour charge for Airconditioned Function Hall',
                'category' => 'room_service',
                'price' => 500.00,
                'duration' => 60,
                'capacity' => null,
                'is_available' => 1,
            ],
            [
                'name' => 'Function Hall - Succeeding Hour (Non-AC)',
                'description' => 'Additional hour charge for Non-Airconditioned Function Hall',
                'category' => 'room_service',
                'price' => 450.00,
                'duration' => 60,
                'capacity' => null,
                'is_available' => 1,
            ],
            [
                'name' => 'Function Hall - Excess Person (AC)',
                'description' => 'Additional charge per person beyond 100 pax for AC Function Hall',
                'category' => 'room_service',
                'price' => 30.00,
                'duration' => null,
                'capacity' => 1,
                'is_available' => 1,
            ],
            [
                'name' => 'Function Hall - Excess Person (Non-AC)',
                'description' => 'Additional charge per person beyond 100 pax for Non-AC Function Hall',
                'category' => 'room_service',
                'price' => 20.00,
                'duration' => null,
                'capacity' => 1,
                'is_available' => 1,
            ],
            [
                'name' => 'LCD Projector',
                'description' => 'LCD Projector rental per hour',
                'category' => 'room_service',
                'price' => 1200.00,
                'duration' => 60,
                'capacity' => null,
                'is_available' => 1,
            ],
            [
                'name' => 'Videoke',
                'description' => 'Videoke machine rental',
                'category' => 'activities',
                'price' => 1000.00,
                'duration' => null,
                'capacity' => null,
                'is_available' => 1,
            ],
            [
                'name' => 'Electric Appliance/Gadget (Small)',
                'description' => 'Charge for small electric appliances or gadgets',
                'category' => 'room_service',
                'price' => 100.00,
                'duration' => null,
                'capacity' => null,
                'is_available' => 1,
            ],
            [
                'name' => 'Electric Appliance/Gadget (Large)',
                'description' => 'Charge for large electric appliances or gadgets',
                'category' => 'room_service',
                'price' => 200.00,
                'duration' => null,
                'capacity' => null,
                'is_available' => 1,
            ],
            
            // Dining - Corkage Fees
            [
                'name' => 'Corkage - Excess Drinks (per case)',
                'description' => 'Corkage fee for more than 4 cases of soft drinks or beer. No corkage on first 4 cases. No corkage on food and lechon.',
                'category' => 'dining',
                'price' => 100.00,
                'duration' => null,
                'capacity' => null,
                'is_available' => 1,
            ],
            
            // Activities - Jetski Rentals - Jetski I
            [
                'name' => 'Jetski I - 15 minutes',
                'description' => 'Jetski I rental for 15 minutes',
                'category' => 'activities',
                'price' => 400.00,
                'duration' => 15,
                'capacity' => 2,
                'is_available' => 1,
            ],
            [
                'name' => 'Jetski I - 30 minutes',
                'description' => 'Jetski I rental for 30 minutes',
                'category' => 'activities',
                'price' => 800.00,
                'duration' => 30,
                'capacity' => 2,
                'is_available' => 1,
            ],
            [
                'name' => 'Jetski I - 1 hour',
                'description' => 'Jetski I rental for 1 hour',
                'category' => 'activities',
                'price' => 1600.00,
                'duration' => 60,
                'capacity' => 2,
                'is_available' => 1,
            ],
            
            // Activities - Jetski Rentals - Jetski II
            [
                'name' => 'Jetski II - 15 minutes',
                'description' => 'Jetski II rental for 15 minutes',
                'category' => 'activities',
                'price' => 500.00,
                'duration' => 15,
                'capacity' => 2,
                'is_available' => 1,
            ],
            [
                'name' => 'Jetski II - 30 minutes',
                'description' => 'Jetski II rental for 30 minutes',
                'category' => 'activities',
                'price' => 1000.00,
                'duration' => 30,
                'capacity' => 2,
                'is_available' => 1,
            ],
            [
                'name' => 'Jetski II - 1 hour',
                'description' => 'Jetski II rental for 1 hour',
                'category' => 'activities',
                'price' => 2000.00,
                'duration' => 60,
                'capacity' => 2,
                'is_available' => 1,
            ],
        ];

        foreach ($services as $service) {
            DB::table('services')->insert([
                'name' => $service['name'],
                'description' => $service['description'],
                'category' => $service['category'],
                'price' => $service['price'],
                'duration' => $service['duration'],
                'capacity' => $service['capacity'],
                'is_available' => $service['is_available'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('');
        $this->command->info('✅ Vales Beach Resort rates seeded successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   - 28 Rooms/Cottages/Halls created');
        $this->command->info('   - 20 Services/Add-ons created');
        $this->command->info('');
        $this->command->table(
            ['Category', 'Count', 'Price Range'],
            [
                ['Umbrella Cottages', '10', '₱350 - ₱400'],
                ['Bahay Kubo Cottages', '8', '₱200 - ₱250'],
                ['Beer Garden', '1', '₱2,000 - ₱2,500'],
                ['Dining Hall', '1', '₱4,000 - ₱4,500'],
                ['Function Halls', '2', '₱4,500 - ₱5,000'],
                ['Standard Rooms', '6', '₱1,000'],
                ['Executive Cottage', '1', '₱7,500'],
                ['Jetski Rentals', '6', '₱400 - ₱2,000'],
                ['Additional Services', '14', '₱5 - ₱1,200'],
            ]
        );
    }
}
