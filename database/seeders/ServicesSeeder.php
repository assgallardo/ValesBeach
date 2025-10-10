<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Spa Massage',
                'description' => 'Relaxing full body massage with essential oils. Perfect for unwinding after a long day at the beach.',
                'category' => 'spa',
                'price' => 1500.00,
                'duration' => 60,
                'capacity' => 1,
                'is_available' => true,
            ],
            [
                'name' => 'Airport Transfer',
                'description' => 'Comfortable and convenient transportation from/to the airport. Includes meet and greet service.',
                'category' => 'transportation',
                'price' => 800.00,
                'duration' => null,
                'capacity' => 4,
                'is_available' => true,
            ],
            [
                'name' => 'Romantic Dinner',
                'description' => 'Private beachfront dinner for two with candles and live acoustic music. Includes 3-course meal.',
                'category' => 'dining',
                'price' => 3500.00,
                'duration' => 120,
                'capacity' => 2,
                'is_available' => true,
            ],
            [
                'name' => 'Island Hopping',
                'description' => 'Full day island hopping tour with lunch, snorkeling gear, and professional guide.',
                'category' => 'activities',
                'price' => 2200.00,
                'duration' => 480,
                'capacity' => 10,
                'is_available' => true,
            ],
            [
                'name' => 'Couples Spa Package',
                'description' => 'Spa treatment for couples including massage, facial, and access to private jacuzzi.',
                'category' => 'spa',
                'price' => 4000.00,
                'duration' => 180,
                'capacity' => 2,
                'is_available' => true,
            ],
            [
                'name' => 'Beach BBQ Buffet',
                'description' => 'All-you-can-eat beachfront BBQ buffet with fresh seafood, grilled meats, and tropical fruits.',
                'category' => 'dining',
                'price' => 1200.00,
                'duration' => null,
                'capacity' => 50,
                'is_available' => true,
            ],
            [
                'name' => 'Sunset Yacht Cruise',
                'description' => 'Luxury yacht cruise during sunset with complimentary drinks and appetizers.',
                'category' => 'activities',
                'price' => 2800.00,
                'duration' => 150,
                'capacity' => 8,
                'is_available' => true,
            ],
            [
                'name' => 'Laundry Service',
                'description' => 'Professional laundry and dry cleaning service with same-day or next-day delivery.',
                'category' => 'room_service',
                'price' => 300.00,
                'duration' => null,
                'capacity' => null,
                'is_available' => true,
            ],
            [
                'name' => 'Personal Chef',
                'description' => 'Private chef service for in-room or villa dining. Customizable menu based on preferences.',
                'category' => 'dining',
                'price' => 5000.00,
                'duration' => null,
                'capacity' => 8,
                'is_available' => false, // Currently unavailable
            ],
            [
                'name' => 'Scuba Diving',
                'description' => 'Guided scuba diving experience with certified instructors. All equipment included.',
                'category' => 'activities',
                'price' => 3200.00,
                'duration' => 180,
                'capacity' => 4,
                'is_available' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            Service::create($serviceData);
        }
    }
}