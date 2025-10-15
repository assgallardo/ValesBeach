<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class ManagerController extends Controller
{
    /**
     * Show the manager dashboard with statistics
     */
    public function dashboard()
    {
        return view('manager.dashboard');
    }

    /**
     * Show staff management page
     */
    public function staff()
    {
        $staff = User::where('role', 'staff')->paginate(15);
        return view('manager.staff.index', compact('staff'));
    }

    /**
     * Show rooms management page
     */
    public function rooms(Request $request)
    {
        // Get all rooms with their images
        $rooms = \App\Models\Room::with(['images'])->orderBy('name')->paginate(12);
        
        return view('manager.rooms', compact('rooms'));
    }

    /**
     * Show guests management page
     */
    public function guests()
    {
        $guests = \App\Models\User::where('role', 'guest')->paginate(15);
        return view('manager.guests.index', compact('guests'));
    }

    /**
     * Show reports page
     */
    public function reports()
    {
        $totalBookings = 0;
        $totalRevenue = 0;
        $monthlyBookings = 0;
        
        if (class_exists('App\Models\Booking')) {
            try {
                $totalBookings = Booking::count();
                
                // Try different possible column names for total price
                $priceColumn = 'total_price';
                if (Schema::hasColumn('bookings', 'total_amount')) {
                    $priceColumn = 'total_amount';
                } elseif (Schema::hasColumn('bookings', 'price')) {
                    $priceColumn = 'price';
                }
                
                $totalRevenue = Booking::where('status', 'completed')->sum($priceColumn);
                $monthlyBookings = Booking::whereMonth('created_at', now()->month)->count();
            } catch (\Exception $e) {
                $totalBookings = 0;
                $totalRevenue = 0;
                $monthlyBookings = 0;
            }
        }
        
        return view('manager.reports.index', compact('totalBookings', 'totalRevenue', 'monthlyBookings'));
    }

    /**
     * Show maintenance page
     */
    public function maintenance()
    {
        return view('manager.maintenance.index');
    }

    /**
     * Show inventory page
     */
    public function inventory()
    {
        return view('manager.inventory.index');
    }

    /**
     * Show all bookings with filtering and statistics
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'room']);

        // Get the correct column names
        $checkinColumn = $this->getCheckinColumn();
        $checkoutColumn = $this->getCheckoutColumn();
        $guestsColumn = $this->getGuestsColumn();
        $priceColumn = $this->getPriceColumn();
        $referenceColumn = $this->getReferenceColumn();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $referenceColumn) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                });
                
                if ($referenceColumn && Schema::hasColumn('bookings', $referenceColumn)) {
                    $q->orWhere($referenceColumn, 'like', "%{$search}%");
                }
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->filled('date') && $checkinColumn) {
            $query->whereDate($checkinColumn, $request->date);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        // Calculate statistics safely
        $stats = $this->calculateBookingStats();

        $statuses = ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled'];

        return view('manager.bookings.index', compact('bookings', 'stats', 'statuses'));
    }

    private function getCheckinColumn()
    {
        if (Schema::hasColumn('bookings', 'check_in_date')) return 'check_in_date';
        if (Schema::hasColumn('bookings', 'checkin_date')) return 'checkin_date';
        if (Schema::hasColumn('bookings', 'check_in')) return 'check_in';
        if (Schema::hasColumn('bookings', 'start_date')) return 'start_date';
        return null;
    }

    private function getCheckoutColumn()
    {
        if (Schema::hasColumn('bookings', 'check_out_date')) return 'check_out_date';
        if (Schema::hasColumn('bookings', 'checkout_date')) return 'checkout_date';
        if (Schema::hasColumn('bookings', 'check_out')) return 'check_out';
        if (Schema::hasColumn('bookings', 'end_date')) return 'end_date';
        return null;
    }

    private function getGuestsColumn()
    {
        if (Schema::hasColumn('bookings', 'guests')) return 'guests';
        if (Schema::hasColumn('bookings', 'guest_count')) return 'guest_count';
        if (Schema::hasColumn('bookings', 'number_of_guests')) return 'number_of_guests';
        return null;
    }

    private function getPriceColumn()
    {
        if (Schema::hasColumn('bookings', 'total_price')) return 'total_price';
        if (Schema::hasColumn('bookings', 'total_amount')) return 'total_amount';
        if (Schema::hasColumn('bookings', 'price')) return 'price';
        if (Schema::hasColumn('bookings', 'amount')) return 'amount';
        return null;
    }

    private function getReferenceColumn()
    {
        if (Schema::hasColumn('bookings', 'booking_reference')) return 'booking_reference';
        if (Schema::hasColumn('bookings', 'reference')) return 'reference';
        if (Schema::hasColumn('bookings', 'booking_id')) return 'booking_id';
        return null;
    }

    private function calculateBookingStats()
    {
        try {
            $checkinColumn = $this->getCheckinColumn();
            $checkoutColumn = $this->getCheckoutColumn();
            $priceColumn = $this->getPriceColumn();

            $stats = [
                'total_bookings' => Booking::count(),
                'pending_bookings' => Booking::where('status', 'pending')->count(),
                'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
                'checked_in' => Booking::where('status', 'checked_in')->count(),
                'completed_bookings' => Booking::where('status', 'completed')->count(),
                'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
                'total_revenue' => $priceColumn ? Booking::where('status', 'completed')->sum($priceColumn) : 0,
                'todays_checkins' => 0,
                'todays_checkouts' => 0,
            ];

            // Calculate today's check-ins and check-outs if columns exist
            if ($checkinColumn) {
                $stats['todays_checkins'] = Booking::whereDate($checkinColumn, today())
                                                  ->where('status', 'confirmed')
                                                  ->count();
            }

            if ($checkoutColumn) {
                $stats['todays_checkouts'] = Booking::whereDate($checkoutColumn, today())
                                                   ->where('status', 'checked_in')
                                                   ->count();
            }

            return $stats;
        } catch (\Exception $e) {
            // Return default stats if there's an error
            return [
                'total_bookings' => 0,
                'pending_bookings' => 0,
                'confirmed_bookings' => 0,
                'checked_in' => 0,
                'completed_bookings' => 0,
                'cancelled_bookings' => 0,
                'total_revenue' => 0,
                'todays_checkins' => 0,
                'todays_checkouts' => 0,
            ];
        }
    }

    /**
     * Show create booking form
     */
    public function createBooking()
    {
        $users = \App\Models\User::all();
        $rooms = \App\Models\Room::all();
        return view('manager.bookings.create', compact('users', 'rooms'));
    }

    /**
     * Store a new booking
     */
    public function storeBooking(Request $request)
    {
        $checkinColumn = $this->getCheckinColumn() ?: 'check_in_date';
        $checkoutColumn = $this->getCheckoutColumn() ?: 'check_out_date';
        $guestsColumn = $this->getGuestsColumn() ?: 'guests';
        $priceColumn = $this->getPriceColumn() ?: 'total_price';
        $referenceColumn = $this->getReferenceColumn() ?: 'booking_reference';

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1|max:10',
            'total_price' => 'required|numeric|min:0',
            'special_requests' => 'nullable|string',
        ]);

        $bookingData = [
            'user_id' => $request->user_id,
            'room_id' => $request->room_id,
            $checkinColumn => $request->check_in_date,
            $checkoutColumn => $request->check_out_date,
            $guestsColumn => $request->guests,
            $priceColumn => $request->total_price,
            'status' => 'confirmed',
        ];

        // Add reference if column exists
        if (Schema::hasColumn('bookings', $referenceColumn)) {
            $bookingData[$referenceColumn] = 'VB' . strtoupper(substr(uniqid(), -8));
        }

        // Add special requests if column exists
        if (Schema::hasColumn('bookings', 'special_requests')) {
            $bookingData['special_requests'] = $request->special_requests;
        }

        Booking::create($bookingData);

        return redirect()->route('manager.bookings.index')
                        ->with('success', 'Booking created successfully.');
    }

    /**
     * Show booking details
     */
    public function showBooking($id)
    {
        $booking = Booking::with(['user', 'room'])->findOrFail($id);
        return view('manager.bookings.show', compact('booking'));
    }

    /**
     * Show reservation details
     */
    public function showReservation($id)
    {
        $booking = Booking::with(['user', 'room'])->findOrFail($id);
        return view('manager.reservations.show', compact('booking'));
    }

    /**
     * Show edit booking form
     */
    public function editBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $rooms = Room::where('status', 'available')->get();
        $guests = User::where('role', 'guest')->get();
        return view('manager.bookings.edit', compact('booking', 'rooms', 'guests'));
    }

    /**
     * Update booking
     */
    public function updateBooking(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'required|integer|min:1',
                'special_requests' => 'nullable|string'
            ]);

            // Calculate total price
            $room = Room::findOrFail($request->room_id);
            $checkIn = new \DateTime($request->check_in);
            $checkOut = new \DateTime($request->check_out);
            $nights = $checkIn->diff($checkOut)->days;
            $totalPrice = $nights * $room->price;

            // Update booking
            $booking->update([
                'room_id' => $request->room_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'guests' => $request->guests,
                'total_price' => $totalPrice,
                'special_requests' => $request->special_requests
            ]);

            // Return JSON response for AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking updated successfully!',
                    'booking' => $booking->fresh(['user', 'room'])
                ]);
            }

            return redirect()->route('manager.bookings.index')
                            ->with('success', 'Booking updated successfully!');
                            
        } catch (\Exception $e) {
            \Log::error('Booking update error: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update booking: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                            ->with('error', 'Failed to update booking: ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Update booking status only
     */
    public function updateBookingStatus(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            $request->validate([
                'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,completed'
            ]);

            $booking->update(['status' => $request->status]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking status updated successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Booking status updated successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Status update error: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update status.');
        }
    }

    /**
     * Confirm a booking
     */
    public function confirmBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'confirmed']);
        return redirect()->back()->with('success', 'Booking confirmed successfully.');
    }

    /**
     * Check in a guest
     */
    public function checkinBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'checked_in']);
        return redirect()->back()->with('success', 'Guest checked in successfully.');
    }

    /**
     * Check out a guest
     */
    public function checkoutBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'completed']);
        return redirect()->back()->with('success', 'Guest checked out successfully.');
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Show create booking form for specific room
     */
    public function createFromRoom($roomId)
    {
        $room = \App\Models\Room::findOrFail($roomId);
        $guests = \App\Models\User::where('role', 'user')->get();
        
        // Get available rooms (in case user wants to change room)
        $rooms = \App\Models\Room::where('status', 'available')->get();
        
        return view('manager.bookings.create-from-room', compact('room', 'guests', 'rooms'));
    }

    /**
     * Services management page
     */
    public function services()
    {
        return view('manager.services');
    }

    /**
     * Calendar management page
     */
    public function calendar(Request $request)
    {
        $checkinColumn = $this->getCheckinColumn() ?: 'check_in_date';
        $checkoutColumn = $this->getCheckoutColumn() ?: 'check_out_date';

        // Get month/year from request or default to current
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // Start and end of the calendar month
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $bookings = Booking::with(['user', 'room'])
            ->where('status', '!=', 'cancelled')
            ->whereDate($checkinColumn, '<=', $endDate)
            ->whereDate($checkoutColumn, '>=', $startDate)
            ->get();

        $rooms = Room::orderBy('name')->get();

        $totalRooms = $rooms->count();
        $occupiedRooms = $bookings->where('status', 'confirmed')
            ->where($checkinColumn, '<=', now())
            ->where($checkoutColumn, '>=', now())
            ->count();
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        // Prepare bookings data for JS (for calendar rendering)
        $bookingsData = $bookings->map(function($booking) use ($checkinColumn, $checkoutColumn) {
            return [
                'check_in_date' => $booking->$checkinColumn,
                'check_out_date' => $booking->$checkoutColumn,
                'guest_name' => $booking->user->name ?? 'Guest',
                'room_name' => $booking->room->name ?? 'N/A',
                'status' => $booking->status
            ];
        });

        return view('manager.calendar', compact(
            'bookings',
            'rooms',
            'totalRooms',
            'occupiedRooms',
            'occupancyRate',
            'checkinColumn',
            'checkoutColumn',
            'bookingsData',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show create room form
     */
    public function createRoom()
    {
        // Return a view for creating a room
        return view('manager.rooms.create');
    }

    /**
     * Store a new room
     */
    public function storeRoom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|exists:room_types,id',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $roomData = $request->only('name', 'type', 'price', 'status', 'description');

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('room_images', 'public');
            $roomData['image'] = $path;
        }

        Room::create($roomData);

        return redirect()->route('manager.rooms')->with('success', 'Room created successfully!');
    }

    /**
     * Show room details
     */
    public function showRoom($id)
    {
        $room = Room::with('images')->findOrFail($id);
        return view('manager.rooms.show', compact('room'));
    }
}
