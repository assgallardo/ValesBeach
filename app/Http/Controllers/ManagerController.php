<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
        $query = \App\Models\Room::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Apply price range filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Apply availability filter
        if ($request->filled('is_available')) {
            $query->where('is_available', $request->is_available === '1');
        }

        // Apply sorting
        $sortField = in_array($request->input('sort_by'), ['number', 'name', 'type', 'price', 'created_at']) 
            ? $request->input('sort_by') 
            : 'name';
            
        $sortOrder = in_array(strtolower($request->input('sort_order')), ['asc', 'desc']) 
            ? strtolower($request->input('sort_order')) 
            : 'asc';

        $query->orderBy($sortField, $sortOrder);

        // Get paginated results
        $rooms = $query->paginate(10)->withQueryString();

        // Get available types for filter dropdown
        $types = \App\Models\Room::distinct()->pluck('type');

        return view('manager.rooms.index', [
            'rooms' => $rooms,
            'types' => $types,
        ]);
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
        // All bookings query (no category filter)
        $allQuery = Booking::with(['user', 'room', 'payments']);

        // Room bookings query (category = 'Rooms')
        $roomQuery = Booking::with(['user', 'room', 'payments'])
            ->whereHas('room', function($q) {
                $q->where('category', 'Rooms');
            });

        // Cottage bookings query (category = 'Cottages')
        $cottageQuery = Booking::with(['user', 'room', 'payments'])
            ->whereHas('room', function($q) {
                $q->where('category', 'Cottages');
            });

        // Events & Dining bookings query (category = 'Event and Dining')
        $eventDiningQuery = Booking::with(['user', 'room', 'payments'])
            ->whereHas('room', function($q) {
                $q->where('category', 'Event and Dining');
            });

        // Get the correct column names
        $checkinColumn = $this->getCheckinColumn();
        $checkoutColumn = $this->getCheckoutColumn();
        $guestsColumn = $this->getGuestsColumn();
        $priceColumn = $this->getPriceColumn();
        $referenceColumn = $this->getReferenceColumn();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $searchFilter = function($q) use ($search, $referenceColumn) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                });
                
                if ($referenceColumn && Schema::hasColumn('bookings', $referenceColumn)) {
                    $q->orWhere($referenceColumn, 'like', "%{$search}%");
                }
            };
            $allQuery->where($searchFilter);
            $roomQuery->where($searchFilter);
            $cottageQuery->where($searchFilter);
            $eventDiningQuery->where($searchFilter);
        }

        // Status filter
        if ($request->filled('status')) {
            $allQuery->where('status', $request->status);
            $roomQuery->where('status', $request->status);
            $cottageQuery->where('status', $request->status);
            $eventDiningQuery->where('status', $request->status);
        }

        // Category filter
        if ($request->filled('category')) {
            $allQuery->whereHas('room', function($q) use ($request) {
                $q->where('category', $request->category);
            });
            $roomQuery->whereHas('room', function($q) use ($request) {
                $q->where('category', $request->category);
            });
            $cottageQuery->whereHas('room', function($q) use ($request) {
                $q->where('category', $request->category);
            });
            $eventDiningQuery->whereHas('room', function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // Date filter
        if ($request->filled('date') && $checkinColumn) {
            $allQuery->whereDate($checkinColumn, $request->date);
            $roomQuery->whereDate($checkinColumn, $request->date);
            $cottageQuery->whereDate($checkinColumn, $request->date);
            $eventDiningQuery->whereDate($checkinColumn, $request->date);
        }

        $allBookings = $allQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'all_page');
        $bookings = $roomQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'room_page');
        $cottageBookings = $cottageQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'cottage_page');
        $eventDiningBookings = $eventDiningQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'event_page');

        // Calculate statistics safely
        $stats = $this->calculateBookingStats();

        $statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'completed', 'cancelled'];

        return view('manager.bookings.index', compact('allBookings', 'bookings', 'cottageBookings', 'eventDiningBookings', 'stats', 'statuses'));
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
        // Check if it's a new guest or existing guest
        $isNewGuest = $request->filled('guest_name') && $request->filled('guest_email');

        if ($isNewGuest) {
            // Validate for new guest creation
            $request->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|unique:users,email',
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after_or_equal:check_in',
                'guests' => 'required|integer|min:1',
                'status' => 'required|in:confirmed,pending,checked_in,checked_out,cancelled',
            ]);

            // Create new guest user
            $user = User::create([
                'name' => $request->guest_name,
                'email' => $request->guest_email,
                'password' => bcrypt(Str::random(16)), // Random password
                'role' => 'guest',
            ]);

            $userId = $user->id;
        } else {
            // Validate for existing guest
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after_or_equal:check_in',
                'guests' => 'required|integer|min:1',
                'status' => 'required|in:confirmed,pending,checked_in,checked_out,cancelled',
            ]);

            $userId = $request->user_id;
        }

        // Get room to calculate price
        $room = Room::findOrFail($request->room_id);
        
        // Calculate nights and total price
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkOut->diffInDays($checkIn);
        
        // Same-day booking counts as 1 night
        if ($nights == 0) {
            $nights = 1;
        }
        
        $totalPrice = $room->price * $nights;

        // Create booking
        $booking = Booking::create([
            'user_id' => $userId,
            'room_id' => $request->room_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'guests' => $request->guests,
            'total_price' => $totalPrice,
            'status' => $request->status,
        ]);

        return redirect()->route('manager.bookings.index')
                        ->with('success', 'Booking created successfully for ' . $room->name . '!');
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
     * Check room availability for booking dates
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after_or_equal:check_in',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        // Check for conflicting bookings (exclude current booking if provided)
        $query = Booking::where('room_id', $request->room_id)
            ->where('status', '!=', 'cancelled');
        
        if ($request->has('booking_id')) {
            $query->where('id', '!=', $request->booking_id);
        }
        
        $existingBooking = $query->where(function ($q) use ($request) {
                $q->whereBetween('check_in', [$request->check_in, $request->check_out])
                  ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                  ->orWhere(function ($subQuery) use ($request) {
                      $subQuery->where('check_in', '<=', $request->check_in)
                                ->where('check_out', '>=', $request->check_out);
                  });
            })
            ->first();

        return response()->json([
            'available' => !$existingBooking,
            'message' => $existingBooking ? 'This facility already has a booking for the selected dates.' : 'Facility is available.'
        ]);
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
                'check_out' => 'required|date|after_or_equal:check_in',
                'guests' => 'required|integer|min:1',
                'special_requests' => 'nullable|string'
            ]);

            // Calculate total price
            $room = Room::findOrFail($request->room_id);
            $checkIn = new \DateTime($request->check_in);
            $checkIn->setTime(0, 0, 0);
            $checkOut = new \DateTime($request->check_out);
            $checkOut->setTime(0, 0, 0);
            
            // Calculate nights: same day = 1 night, otherwise count days difference
            $daysDiff = $checkIn->diff($checkOut)->days;
            $nights = $daysDiff === 0 ? 1 : $daysDiff; // Same day counts as 1 night
            
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

            $oldStatus = $booking->status;
            $booking->update(['status' => $request->status]);

            // Handle housekeeping tasks based on status
            if ($request->status === 'checked_out') {
                // Check if housekeeping task already exists
                $existingTask = \App\Models\Task::where('booking_id', $booking->id)
                    ->where('task_type', 'housekeeping')
                    ->first();
                
                // Only create if no task exists
                if (!$existingTask) {
                    $this->createHousekeepingTask($booking);
                }
            } 
            elseif ($request->status === 'completed' && $oldStatus === 'checked_out') {
                // When booking completed from checked_out, mark task as completed
                \App\Models\Task::where('booking_id', $booking->id)
                    ->where('task_type', 'housekeeping')
                    ->where('status', '!=', 'completed')
                    ->update(['status' => 'completed', 'completed_at' => now()]);
            }
            elseif (!in_array($request->status, ['checked_out', 'completed'])) {
                // Remove non-completed housekeeping tasks if status is not checked_out or completed
                \App\Models\Task::where('booking_id', $booking->id)
                    ->where('task_type', 'housekeeping')
                    ->where('status', '!=', 'completed')
                    ->delete();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking status updated successfully!' . ($request->status === 'checked_out' ? ' Housekeeping task created.' : '')
                ]);
            }

            return redirect()->back()->with('success', 'Booking status updated successfully!' . ($request->status === 'checked_out' ? ' Housekeeping task created.' : ''));
            
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
     * Create housekeeping task for checked-out booking
     */
    private function createHousekeepingTask($booking)
    {
        try {
            // Check if a housekeeping task already exists for this booking
            $existingTask = \App\Models\Task::where('booking_id', $booking->id)
                ->where('task_type', 'housekeeping')
                ->first();
            
            if ($existingTask) {
                \Log::info('Housekeeping task already exists', [
                    'task_id' => $existingTask->id,
                    'booking_id' => $booking->id,
                    'room' => $booking->room->name
                ]);
                return $existingTask;
            }
            
            // Format check-out datetime with facility time
            $checkOutDate = $booking->check_out->format('M d, Y');
            $checkOutTime = $booking->room->check_out_time 
                ? \Carbon\Carbon::parse($booking->room->check_out_time)->format('g:i A')
                : '12:00 AM';
            $checkOutDisplay = "{$checkOutDate} {$checkOutTime}";
            
            $task = \App\Models\Task::create([
                'title' => 'Housekeeping Required',
                'description' => "Room cleanup required after guest check-out.\n\nFacility: {$booking->room->name}\nCategory: {$booking->room->category}\nGuest: {$booking->user->name}\nCheck-out: {$checkOutDisplay}",
                'booking_id' => $booking->id,
                'task_type' => 'housekeeping',
                'assigned_by' => auth()->id(),
                'assigned_to' => null, // Initially unassigned
                'status' => 'pending',
                'due_date' => now()->addHours(2), // 2 hours to complete housekeeping
            ]);

            \Log::info('Housekeeping task created', [
                'task_id' => $task->id,
                'booking_id' => $booking->id,
                'room' => $booking->room->name
            ]);

            return $task;
        } catch (\Exception $e) {
            \Log::error('Failed to create housekeeping task: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update booking payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        \Log::info('Payment status update requested (Manager)', [
            'booking_id' => $id,
            'requested_status' => $request->payment_status,
            'request_data' => $request->all()
        ]);

        \DB::beginTransaction();
        try {
            $booking = Booking::findOrFail($id);
            
            $request->validate([
                'payment_status' => 'required|in:unpaid,partial,paid,cancelled'
            ]);

            $oldStatus = $booking->payment_status;
            
            // Update booking payment status
            $booking->update(['payment_status' => $request->payment_status]);

            $paymentsUpdated = 0;

            // Update all related payment records' status
            if ($request->payment_status === 'paid') {
                // If marking as paid, set all payments to completed
                $paymentsUpdated = $booking->payments()->update(['status' => 'completed']);
            } elseif ($request->payment_status === 'cancelled') {
                // If marking as cancelled, set all payments to cancelled
                $paymentsUpdated = $booking->payments()->update(['status' => 'cancelled']);
            } elseif ($request->payment_status === 'unpaid') {
                // If marking as unpaid, set all payments to pending
                $paymentsUpdated = $booking->payments()->update(['status' => 'pending']);
            }
            // For 'partial', we keep the existing payment statuses as they are

            // Refresh the booking to get updated data
            $booking->refresh();

            \DB::commit();

            \Log::info('Payment status updated successfully (Manager)', [
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $booking->payment_status,
                'updated_by' => auth()->id(),
                'total_payments' => $booking->payments()->count(),
                'payments_updated' => $paymentsUpdated
            ]);

            $message = "Payment status updated successfully to " . ucfirst($request->payment_status) . ".";
            if ($paymentsUpdated > 0) {
                $message .= " {$paymentsUpdated} payment record(s) have been updated.";
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Payment status update error (Manager)', [
                'booking_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update payment status: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update payment status: ' . $e->getMessage());
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
        $users = \App\Models\User::where('role', 'guest')->get();
        
        return view('manager.bookings.create-from-room', compact('room', 'users'));
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

        return redirect()->route('manager.rooms.index')->with('success', 'Room created successfully!');
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
