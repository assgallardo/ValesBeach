<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role', 'guest')->get();
        $rooms = Room::all();

        if ($users->isEmpty() || $rooms->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];
        
        // Create some sample bookings
        foreach ($rooms as $room) {
            for ($i = 0; $i < 3; $i++) {
                $checkIn = Carbon::now()->addDays(rand(1, 30));
                $checkOut = $checkIn->copy()->addDays(rand(1, 7));
                
                Booking::create([
                    'user_id' => $users->random()->id,
                    'room_id' => $room->id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'guests' => rand(1, $room->capacity ?? 4),
                    'total_price' => $room->price * $checkIn->diffInDays($checkOut),
                    'status' => $statuses[array_rand($statuses)],
                ]);
            }
        }
    }
}
