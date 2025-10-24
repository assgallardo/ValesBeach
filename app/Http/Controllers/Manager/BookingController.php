<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // Room bookings query
        $roomQuery = Reservation::with(['room'])
            ->whereHas('room', function($q) {
                $q->where('category', 'Rooms');
            });

        // Cottage bookings query
        $cottageQuery = Reservation::with(['room'])
            ->whereHas('room', function($q) {
                $q->where('category', 'Cottages');
            });

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $searchFilter = function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            };
            $roomQuery->where($searchFilter);
            $cottageQuery->where($searchFilter);
        }

        // Status filter
        if ($request->filled('status')) {
            $roomQuery->where('status', $request->status);
            $cottageQuery->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $roomQuery->where('check_in_date', '>=', $request->date_from);
            $cottageQuery->where('check_in_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $roomQuery->where('check_out_date', '<=', $request->date_to);
            $cottageQuery->where('check_out_date', '<=', $request->date_to);
        }

        $reservations = $roomQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'room_page');
        $cottageBookings = $cottageQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'cottage_page');
        
        $statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'completed'];

        return view('manager.bookings.index', compact('reservations', 'cottageBookings', 'statuses'));
    }

    public function show(Reservation $reservation)
    {
        return view('manager.reservations.show', compact('reservation'));
    }

    public function create()
    {
        $rooms = Room::where('is_available', true)->get();
        $users = User::where('role', 'customer')->get();
        
        return view('manager.bookings.create', compact('rooms', 'users'));
    }

    public function createFromRoom(Room $room)
    {
        if (!$room->is_available) {
            return redirect()->route('manager.bookings.index')
                           ->with('error', 'This room is not available for booking.');
        }

        $users = User::where('role', 'customer')->get();
        
        return view('manager.bookings.create-from-room', compact('room', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
        ]);

        // Calculate total price
        $room = Room::findOrFail($request->room_id);
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = $room->price * $nights;

        $reservation = Reservation::create([
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'room_id' => $request->room_id,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'guests' => $request->guests,
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        return redirect()->route('manager.bookings.index')
                        ->with('success', 'Reservation created successfully.');
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,completed'
        ]);

        $reservation->update(['status' => $request->status]);

        return redirect()->route('manager.bookings.index')
                        ->with('success', 'Reservation status updated successfully.');
    }
}