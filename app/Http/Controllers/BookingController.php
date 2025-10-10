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
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'nullable|integer|min:1|max:' . $room->capacity
        ]);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = $room->price * $nights;

        // Check if room is available for these dates
        $isAvailable = !$room->bookings()
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out', [$checkIn, $checkOut])
                      ->orWhere(function($q) use ($checkIn, $checkOut) {
                          $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                      });
            })
            ->where('status', '!=', 'cancelled')
            ->exists();

        if (!$isAvailable) {
            return back()->withErrors(['message' => 'Room not available for those dates.']);
        }

        // Create the booking
        $booking = $room->bookings()->create([
            'user_id' => auth()->id(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_price' => $totalPrice,
            'guests' => $request->guests ?? 1,  // Set default to 1 if not provided
            'status' => 'pending'
        ]);

        return redirect()
            ->route('guest.bookings')
            ->with('success', 'Room booked!');
    }

    /**
     * Display user's bookings.
     */
    public function myBookings()
    {
        $bookings = auth()->user()->bookings()
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
        // Completed transactions cannot be cancelled
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            $message = $booking->status === 'completed' 
                ? 'Completed transactions cannot be cancelled' 
                : 'This booking cannot be cancelled';
            return back()->withErrors(['status' => $message]);
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled.');
    }

    /**
     * Show booking history for the authenticated user.
     */
    public function history()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['room', 'payments', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('guest.bookings.history', compact('bookings'));
    }
}
