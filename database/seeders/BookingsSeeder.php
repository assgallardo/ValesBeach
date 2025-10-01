<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;
use App\Models\Service;
use Carbon\Carbon;

class BookingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure we have users and rooms
        $guests = User::where('role', 'guest')->get();
        $rooms = Room::all();
        $services = Service::all();

        if ($guests->isEmpty() || $rooms->isEmpty()) {
            $this->command->info('Skipping bookings seeder - no guests or rooms found.');
            return;
        }

        $bookings = [
            [
                'user_id' => $guests->random()->id,
                'room_id' => $rooms->random()->id,
                'check_in' => Carbon::now()->addDays(5),
                'check_out' => Carbon::now()->addDays(8),
                'guests' => 2,
                'special_requests' => 'Late check-in requested',
                'total_price' => 7500.00,
                'status' => 'pending',
            ],
            [
                'user_id' => $guests->random()->id,
                'room_id' => $rooms->random()->id,
                'check_in' => Carbon::now()->addDays(10),
                'check_out' => Carbon::now()->addDays(14),
                'guests' => 4,
                'special_requests' => 'Celebrating anniversary',
                'total_price' => 12000.00,
                'status' => 'confirmed',
            ],
            [
                'user_id' => $guests->random()->id,
                'room_id' => $rooms->random()->id,
                'check_in' => Carbon::now()->subDays(2),
                'check_out' => Carbon::now()->addDays(2),
                'guests' => 2,
                'special_requests' => null,
                'total_price' => 8000.00,
                'status' => 'checked_in',
            ],
            [
                'user_id' => $guests->random()->id,
                'room_id' => $rooms->random()->id,
                'check_in' => Carbon::now()->subDays(10),
                'check_out' => Carbon::now()->subDays(7),
                'guests' => 3,
                'special_requests' => 'Extra towels',
                'total_price' => 9500.00,
                'status' => 'completed',
            ],
            [
                'user_id' => $guests->random()->id,
                'room_id' => $rooms->random()->id,
                'check_in' => Carbon::now()->addDays(15),
                'check_out' => Carbon::now()->addDays(18),
                'guests' => 1,
                'special_requests' => 'Business traveler',
                'total_price' => 4500.00,
                'status' => 'cancelled',
            ],
        ];

        foreach ($bookings as $bookingData) {
            $booking = Booking::create($bookingData);
            
            // Attach random services to some bookings
            if ($services->isNotEmpty() && rand(0, 1)) {
                $randomServices = $services->random(rand(1, 2));
                foreach ($randomServices as $service) {
                    $booking->services()->attach($service->id, [
                        'quantity' => 1,
                        'unit_price' => $service->price,
                        'total_price' => $service->price,
                    ]);
                }
            }
        }

        $this->command->info('Successfully seeded ' . count($bookings) . ' bookings!');
    }
}
