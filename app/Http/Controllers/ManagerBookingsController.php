<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User; // Add this import
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception; // Add this import

class ManagerBookingsController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'room']); // Only load existing relationships

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('check_in', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('check_out', '<=', $request->get('date_to'));
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Fetch Cottage Bookings with similar filters
        $cottageQuery = \App\Models\CottageBooking::with(['user', 'cottage']);

        // Apply similar filters for cottage bookings
        if ($request->filled('search')) {
            $search = $request->get('search');
            $cottageQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $cottageQuery->where('status', $request->get('status'));
        }

        if ($request->filled('date_from')) {
            $cottageQuery->whereDate('check_in_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $cottageQuery->whereDate('check_out_date', '<=', $request->get('date_to'));
        }

        $cottageBookings = $cottageQuery->orderBy('created_at', 'desc')->paginate(15);

        return view('manager.bookings.index', compact('bookings', 'cottageBookings'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create(Request $request)
    {
        $rooms = Room::where('is_available', true)->orderBy('name')->get();
        $services = Service::where('is_available', true)->orderBy('category')->orderBy('name')->get();
        $guests = User::where('role', 'guest')->where('status', 'active')->orderBy('name')->get();
        
        $selectedRoom = null;
        if ($request->has('room_id')) {
            $selectedRoom = Room::find($request->room_id);
        }

        return view('manager.bookings.create', compact('rooms', 'services', 'guests', 'selectedRoom'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        // Determine if we're creating a new guest or using existing
        $isNewGuest = $request->filled('guest_name') && $request->filled('guest_email');
        
        if ($isNewGuest) {
            $request->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|unique:users,email',
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after_or_equal:check_in',
                'guests' => 'required|integer|min:1',
                'special_requests' => 'nullable|string|max:1000',
                'services' => 'nullable|array',
                'services.*' => 'exists:services,id',
            ]);
            
            // Create new guest user
            $user = User::create([
                'name' => $request->guest_name,
                'email' => $request->guest_email,
                'email_verified_at' => now(),
                'password' => bcrypt('password123'), // Default password
                'role' => 'guest'
            ]);
            
            $userId = $user->id;
        } else {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after_or_equal:check_in',
                'guests' => 'required|integer|min:1',
                'special_requests' => 'nullable|string|max:1000',
                'services' => 'nullable|array',
                'services.*' => 'exists:services,id',
            ]);
            
            $userId = $request->user_id;
        }

        DB::beginTransaction();
        try {
            // Check room availability
            $room = Room::findOrFail($request->room_id);
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            
            // Check for conflicting bookings
            $conflictingBookings = Booking::where('room_id', $request->room_id)
                                         ->whereIn('status', ['confirmed', 'checked_in'])
                                         ->where(function($query) use ($checkIn, $checkOut) {
                                             $query->whereBetween('check_in', [$checkIn, $checkOut])
                                                   ->orWhereBetween('check_out', [$checkIn, $checkOut])
                                                   ->orWhere(function($q) use ($checkIn, $checkOut) {
                                                       $q->where('check_in', '<=', $checkIn)
                                                         ->where('check_out', '>=', $checkOut);
                                                   });
                                         })
                                         ->exists();

            if ($conflictingBookings) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'The selected room is not available for the chosen dates.');
            }

            // Calculate stay duration and total price
            $nights = $checkIn->diffInDays($checkOut);
            // Same-day booking counts as 1 night
            // Note: diffInDays returns float, use == not ===
            if ($nights == 0) {
                $nights = 1;
            }
            $roomTotal = $room->price * $nights;
            
            // Calculate services total
            $servicesTotal = 0;
            $selectedServices = collect();
            if ($request->has('services')) {
                $selectedServices = Service::whereIn('id', $request->services)->get();
                $servicesTotal = $selectedServices->sum('price');
            }

            $totalPrice = $roomTotal + $servicesTotal;

            // Create booking
            $booking = Booking::create([
                'user_id' => $userId,
                'room_id' => $request->room_id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests' => $request->guests,
                'special_requests' => $request->special_requests,
                'total_price' => $totalPrice,
                'status' => 'confirmed', // Manager created bookings are auto-confirmed
            ]);

            // Attach services if any
            if ($request->has('services') && $selectedServices->count() > 0) {
                foreach ($selectedServices as $service) {
                    $booking->services()->attach($service->id, [
                        'quantity' => 1,
                        'unit_price' => $service->price,
                        'total_price' => $service->price,
                    ]);
                }
            }

            DB::commit();

            $successMessage = $isNewGuest 
                ? 'Booking created successfully with new guest account!' 
                : 'Booking created successfully!';

            return redirect()->route('manager.bookings.index')
                           ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'An error occurred while creating the booking: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'room', 'payments.user']);
        return view('manager.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        $rooms = Room::where('is_available', true)->orderBy('name')->get();
        $services = Service::where('is_available', true)->orderBy('category')->orderBy('name')->get();
        $guests = User::where('role', 'guest')->where('status', 'active')->orderBy('name')->get();
        
        $booking->load(['services']);

        return view('manager.bookings.edit', compact('booking', 'rooms', 'services', 'guests'));
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, Booking $booking)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date',
                'check_out' => 'required|date|after_or_equal:check_in',
                'guests' => 'required|integer|min:1|max:20',
                'special_requests' => 'nullable|string|max:1000',
            ]);

            // Get room details
            $room = Room::findOrFail($validated['room_id']);
            
            // Calculate total price
            $checkIn = Carbon::parse($validated['check_in'])->startOfDay();
            $checkOut = Carbon::parse($validated['check_out'])->startOfDay();
            $nights = $checkIn->diffInDays($checkOut);
            
            // Same-day booking counts as 1 night
            // Note: diffInDays returns float, use == not ===
            if ($nights == 0) {
                $nights = 1;
            }
            
            $totalPrice = $room->price * $nights;

            // Update booking
            $booking->update([
                'room_id' => $validated['room_id'],
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests' => $validated['guests'],
                'special_requests' => $validated['special_requests'],
                'total_price' => $totalPrice,
            ]);

            // Load relationships for response
            $booking->load(['user', 'room']);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'booking' => $booking,
                    'message' => 'Booking updated successfully!'
                ]);
            }

            return redirect()->route('manager.bookings.index')->with('success', 'Booking updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all()),
                    'errors' => $e->validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Booking update error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'An error occurred while updating the booking.');
        }
    }

    /**
     * Remove the specified booking.
     */
    /**
     * Delete a booking.
     * Note: All associated payments will be automatically deleted via cascade delete.
     */
    public function destroy(Booking $booking)
    {
        if (in_array($booking->status, ['checked_in', 'completed'])) {
            return redirect()->back()
                           ->with('error', 'Cannot delete a booking that is checked in or completed.');
        }

        DB::beginTransaction();
        try {
            $paymentCount = $booking->payments()->count();
            
            // Detach services (many-to-many relationship)
            $booking->services()->detach();
            
            // Delete the booking (payments will be cascade deleted automatically)
            $booking->delete();
            
            DB::commit();

            $message = $paymentCount > 0
                ? "Booking and {$paymentCount} associated payment(s) deleted successfully!"
                : 'Booking deleted successfully!';

            return redirect()->route('manager.bookings.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Booking deletion failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                           ->with('error', 'An error occurred while deleting the booking: ' . $e->getMessage());
        }
    }

    /**
     * Confirm a pending booking.
     */
    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->back()
                           ->with('error', 'Only pending bookings can be confirmed.');
        }

        $booking->update(['status' => 'confirmed']);

        return redirect()->back()
                       ->with('success', 'Booking confirmed successfully!');
    }

    /**
     * Check in a guest.
     */
    public function checkin(Booking $booking)
    {
        if ($booking->status !== 'confirmed') {
            return redirect()->back()
                           ->with('error', 'Only confirmed bookings can be checked in.');
        }

        $booking->update(['status' => 'checked_in']);

        return redirect()->back()
                       ->with('success', 'Guest checked in successfully!');
    }

    /**
     * Check out a guest.
     */
    public function checkout(Booking $booking)
    {
        if ($booking->status !== 'checked_in') {
            return redirect()->back()
                           ->with('error', 'Only checked-in bookings can be checked out.');
        }

        $booking->update(['status' => 'completed']);

        return redirect()->back()
                       ->with('success', 'Guest checked out successfully!');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Booking $booking)
    {
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return redirect()->back()
                           ->with('error', 'Cannot cancel a completed or already cancelled booking.');
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->back()
                       ->with('success', 'Booking cancelled successfully!');
    }

    /**
     * Update the status of a booking.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,checked_in,completed,cancelled'
            ]);

            $booking->update(['status' => $validated['status']]);
            $booking->load(['user', 'room']); // Only load existing relationships

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'booking' => $booking,
                    'message' => 'Status updated successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Status updated successfully!');

        } catch (\Exception $e) {
            Log::error('Status update error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating status: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error updating status.');
        }
    }

    /**
     * Show the form for creating a new booking from a room.
     */
    public function createFromRoom(Room $room)
    {
        $users = User::where('role', 'guest')->get();
        
        return view('manager.bookings.create-from-room', compact('room', 'users'));
    }
}