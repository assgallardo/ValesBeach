<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of available rooms.
     */
    public function index()
    {
        $rooms = Room::where('is_available', true)->get();
        return view('guest.rooms.index', compact('rooms'));
    }

    /**
     * Show the booking form for a room.
     */
    public function showBookingForm(Room $room)
    {
        return view('guest.rooms.book', compact('room'));
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request, Room $room)
    {
        $validated = $request->validate([
            'check_in' => ['required', 'date', 'after:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:' . $room->capacity],
            'special_requests' => ['nullable', 'string', 'max:500'],
        ]);

        // Check if room is available for these dates
        $conflictingBooking = Booking::where('room_id', $room->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($validated) {
                $query->whereBetween('check_in', [$validated['check_in'], $validated['check_out']])
                    ->orWhereBetween('check_out', [$validated['check_in'], $validated['check_out']]);
            })->exists();

        if ($conflictingBooking) {
            return back()->withErrors(['check_in' => 'Room is not available for these dates']);
        }

        // Calculate number of nights and total price
        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = $room->price * $nights;

        // Create the booking
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'guests' => $validated['guests'],
            'total_price' => $totalPrice,
            'special_requests' => $validated['special_requests'],
            'status' => 'pending'
        ]);

        return redirect()->route('guest.bookings.show', $booking)
            ->with('success', 'Booking created successfully! Please wait for confirmation.');
    }

    /**
     * Display user's bookings.
     */
    public function myBookings()
    {
        $bookings = auth()->user()
            ->bookings()
            ->with('room')
            ->latest()
            ->paginate(10);
        
        return view('guest.bookings.index', compact('bookings'));
    }

    /**
     * Display a specific booking.
     */
    public function show(Booking $booking)
    {
        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        return view('guest.bookings.show', compact('booking'));
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Booking $booking)
    {
        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow cancellation of pending or confirmed bookings
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->withErrors(['status' => 'This booking cannot be cancelled']);
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
