<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Show the staff dashboard
     */
    public function dashboard()
    {
        // Get basic statistics for staff
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('is_available', true)->count(),
        ];

        // Today's check-ins and check-outs
        $today_checkins = Booking::whereDate('check_in', today())
                                ->where('status', 'confirmed')
                                ->with(['user', 'room'])
                                ->get();

        $today_checkouts = Booking::whereDate('check_out', today())
                                 ->where('status', 'checked_in')
                                 ->with(['user', 'room'])
                                 ->get();

        return view('staff.dashboard', compact('stats', 'today_checkins', 'today_checkouts'));
    }

    /**
     * Show tasks page
     */
    public function tasks()
    {
        return view('staff.tasks');
    }

    /**
     * Show schedule page
     */
    public function schedule()
    {
        return view('staff.schedule');
    }

    /**
     * Show maintenance page
     */
    public function maintenance()
    {
        return view('staff.maintenance');
    }
}