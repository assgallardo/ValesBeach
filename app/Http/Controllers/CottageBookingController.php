<?php

namespace App\Http\Controllers;

use App\Models\Cottage;
use App\Models\CottageBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CottageBookingController extends Controller
{
    /**
     * Display a listing of available cottages.
     */
    public function index()
    {
        try {
            $cottages = Cottage::active()
                ->available()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
            
            return view('guest.cottages.index', compact('cottages'));
        } catch (\Exception $e) {
            $cottages = collect([]);
            return view('guest.cottages.index', compact('cottages'))
                ->with('error', 'Unable to load cottages at this time.');
        }
    }

    /**
     * Show cottage details.
     */
    public function show(Cottage $cottage)
    {
        if ($cottage->status !== 'available' || !$cottage->is_active) {
            return redirect()->route('guest.cottages.index')
                ->with('error', 'This cottage is not available for booking.');
        }

        return view('guest.cottages.show', compact('cottage'));
    }

    /**
     * Show the booking form for a cottage.
     */
    public function showBookingForm(Cottage $cottage)
    {
        if ($cottage->status !== 'available' || !$cottage->is_active) {
            return redirect()->route('guest.cottages.index')
                ->with('error', 'This cottage is not available for booking.');
        }

        return view('guest.cottages.book', compact('cottage'));
    }

    /**
     * Store a new cottage booking.
     */
    public function store(Request $request, Cottage $cottage)
    {
        $validated = $request->validate([
            'booking_type' => 'required|in:day_use,overnight,hourly,event',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required_if:booking_type,overnight|date|after:check_in_date',
            'hours' => 'required_if:booking_type,hourly|integer|min:' . ($cottage->min_hours ?? 1) . '|max:' . ($cottage->max_hours ?? 12),
            'guests' => 'required|integer|min:1|max:' . $cottage->capacity,
            'children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $checkInDate = Carbon::parse($validated['check_in_date']);
        $checkOutDate = $validated['booking_type'] === 'overnight' 
            ? Carbon::parse($validated['check_out_date']) 
            : $checkInDate->copy()->addDay();
        
        $hours = $validated['hours'] ?? null;

        // Check availability
        if (!$cottage->isAvailableFor($checkInDate, $checkOutDate)) {
            return back()->with('error', 'This cottage is not available for the selected dates.');
        }

        // Calculate price
        $price = $cottage->calculatePrice(
            $checkInDate, 
            $checkOutDate, 
            $validated['booking_type'],
            $hours
        );

        // Create booking
        $booking = CottageBooking::create([
            'booking_reference' => 'COT-' . strtoupper(Str::random(12)),
            'cottage_id' => $cottage->id,
            'user_id' => auth()->id(),
            'booking_type' => $validated['booking_type'],
            'check_in_date' => $checkInDate,
            'check_in_time' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'check_out_time' => $checkOutDate,
            'hours_booked' => $hours,
            'guests' => $validated['guests'],
            'children' => $validated['children'] ?? 0,
            'special_requests' => $validated['special_requests'] ?? null,
            'base_price' => $price,
            'total_price' => $price,
            'remaining_balance' => $price,
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        return redirect()->route('guest.cottage-bookings.show', $booking)
            ->with('success', 'Cottage booking created successfully! Please proceed with payment.');
    }

    /**
     * Display the cottage booking details.
     */
    public function showBooking(CottageBooking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        return view('guest.cottage-bookings.show', compact('booking'));
    }

    /**
     * Display user's cottage bookings.
     */
    public function myBookings()
    {
        $bookings = CottageBooking::where('user_id', auth()->id())
            ->with('cottage')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guest.cottage-bookings.index', compact('bookings'));
    }

    /**
     * Cancel a cottage booking.
     */
    public function cancel(CottageBooking $booking)
    {
        // Ensure user can only cancel their own bookings
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->cancel('Cancelled by guest');

        return redirect()->route('guest.cottage-bookings.index')
            ->with('success', 'Cottage booking cancelled successfully.');
    }

    /**
     * Check cottage availability (AJAX).
     */
    public function checkAvailability(Request $request, Cottage $cottage)
    {
        $validated = $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);

        $isAvailable = $cottage->isAvailableFor($checkIn, $checkOut);

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable 
                ? 'Cottage is available for selected dates' 
                : 'Cottage is not available for selected dates'
        ]);
    }
}
