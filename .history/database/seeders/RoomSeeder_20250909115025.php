<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'number' => 'SUITE-101',
                'name' => 'Deluxe Ocean View Suite',
                'type' => 'suite',
                'description' => 'Experience luxury with our spacious ocean view suite featuring a private balcony, premium amenities, and breathtaking views of the beach.',
                'price' => 12500.00, // PHP
                'capacity' => 4,
                'beds' => 2,
                'amenities' => json_encode([
                    'Ocean View',
                    'Private Balcony',
                    'King Size Bed',
                    'Sofa Bed',
                    'Mini Bar',
                    'Room Service',
                    'Free Wi-Fi',
                    'Smart TV',
                    'Air Conditioning'
                ]),
                'is_available' => true
            ],
            [
                'number' => 'STD-201',
                'name' => 'Premium Garden Room',
                'type' => 'standard',
                'description' => 'A serene retreat overlooking our tropical gardens, perfect for couples or small families seeking comfort and tranquility.',
                'price' => 8500.00,
                'capacity' => 3,
                'beds' => 1,
                'amenities' => json_encode([
                    'Garden View',
                    'Queen Size Bed',
                    'Work Desk',
                    'Free Wi-Fi',
                    'Smart TV',
                    'Air Conditioning',
                    'Coffee Maker'
                ]),
                'is_available' => true
            ],
            [
                'number' => 'VILLA-301',
                'name' => 'Family Beach Villa',
                'type' => 'villa',
                'description' => 'Spacious beachfront villa with multiple rooms, perfect for families or groups seeking luxury and privacy with direct beach access.',
                'price' => 18500.00,
                'capacity' => 6,
                'beds' => 3,
                'amenities' => json_encode([
                    'Beachfront Location',
                    'Private Pool',
                    'Multiple Bedrooms',
                    'Full Kitchen',
                    'Dining Area',
                    'Living Room',
                    'Free Wi-Fi',
                    'Smart TVs',
                    'Air Conditioning',
                    'BBQ Area'
                ]),
                'is_available' => true
            ],
            [
                'number' => 'SUITE-102',
                'name' => 'Honeymoon Suite',
                'type' => 'suite',
                'description' => 'Romantic suite designed for couples, featuring luxury amenities, stunning ocean views, and special romantic touches.',
                'price' => 15500.00,
                'capacity' => 2,
                'beds' => 1,
                'amenities' => json_encode([
                    'Ocean View',
                    'King Size Bed',
                    'Jacuzzi Tub',
                    'Champagne Service',
                    'Private Balcony',
                    'Room Service',
                    'Free Wi-Fi',
                    'Smart TV',
                    'Air Conditioning',
                    'Mini Bar'
                ]),
                'is_available' => true
            ],
            [
                'name' => 'Standard Mountain View',
                'type' => 'standard',
                'description' => 'Comfortable room with mountain views, perfect for budget-conscious travelers who don\'t want to compromise on quality.',
                'price' => 6500.00,
                'capacity' => 2,
                'beds' => 1,
                'amenities' => json_encode([
                    'Mountain View',
                    'Queen Size Bed',
                    'Free Wi-Fi',
                    'TV',
                    'Air Conditioning',
                    'Basic Toiletries'
                ]),
                'is_available' => true
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
