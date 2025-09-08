<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    /**
     * Display the guest dashboard
     */
    public function dashboard()
    {
        return view('guest.dashboard');
    }

    /**
     * Display the room booking page
     */
    public function rooms()
    {
        $rooms = Room::where('is_visible', true)->get();
        return view('guest.rooms', compact('rooms'));
    }
}
