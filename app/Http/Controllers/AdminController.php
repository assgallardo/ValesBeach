<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function dashboard()
    {
        // Get dashboard statistics
        $stats = [
            'total_users' => User::count(),
            'total_bookings' => Booking::count(),
            'total_rooms' => Room::count(),
            'total_services' => Service::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            'active_users' => User::where('status', 'active')->count(),
            'blocked_users' => User::where('status', 'blocked')->count(),
            'available_rooms' => Room::where('is_available', true)->count(),
            'occupied_rooms' => Room::where('is_available', false)->count(),
        ];

        // Calculate revenue
        $stats['total_revenue'] = Booking::whereIn('status', ['confirmed', 'checked_in', 'completed'])
                                        ->sum('total_price');
        
        $stats['monthly_revenue'] = Booking::whereIn('status', ['confirmed', 'checked_in', 'completed'])
                                          ->whereMonth('created_at', now()->month)
                                          ->whereYear('created_at', now()->year)
                                          ->sum('total_price');

        // Recent bookings
        $recent_bookings = Booking::with(['user', 'room'])
                                 ->orderBy('created_at', 'desc')
                                 ->limit(5)
                                 ->get();

        // Recent users
        $recent_users = User::orderBy('created_at', 'desc')
                           ->limit(5)
                           ->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings', 'recent_users'));
    }

    /**
     * Show users management page
     */
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users', compact('users'));
    }

    /**
     * Toggle user status (active/inactive/blocked)
     */
    public function toggleUserStatus(Request $request, User $user)
    {
        $newStatus = $request->input('status');
        
        // Validate status
        if (!in_array($newStatus, ['active', 'inactive', 'blocked'])) {
            return redirect()->back()->with('error', 'Invalid status provided.');
        }

        // Don't allow admin to block themselves
        if ($user->id === auth()->id() && $newStatus === 'blocked') {
            return redirect()->back()->with('error', 'You cannot block your own account.');
        }

        $user->update(['status' => $newStatus]);

        $message = match($newStatus) {
            'active' => 'User has been activated successfully.',
            'inactive' => 'User has been deactivated successfully.',
            'blocked' => 'User has been blocked successfully.',
        };

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show rooms management page
     */
    public function rooms()
    {
        $rooms = Room::with('images')->orderBy('created_at', 'desc')->paginate(12);
        return view('admin.rooms', compact('rooms'));
    }

    /**
     * Show bookings management page
     */
    public function bookings()
    {
        $bookings = Booking::with(['user', 'room'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);
        
        return view('admin.bookings', compact('bookings'));
    }

    /**
     * Show reports page
     */
    public function reports()
    {
        // Monthly booking statistics
        $monthlyBookings = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total_bookings'),
            DB::raw('SUM(total_price) as total_revenue')
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('month', 'year')
        ->orderBy('month')
        ->get();

        // Popular rooms
        $popularRooms = Room::select('rooms.*', DB::raw('COUNT(bookings.id) as booking_count'))
                           ->leftJoin('bookings', 'rooms.id', '=', 'bookings.room_id')
                           ->groupBy('rooms.id')
                           ->orderBy('booking_count', 'desc')
                           ->limit(10)
                           ->get();

        // User registration statistics
        $userStats = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total_users')
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Revenue by room type
        $revenueByRoomType = Room::select('type', DB::raw('SUM(bookings.total_price) as total_revenue'))
                                ->join('bookings', 'rooms.id', '=', 'bookings.room_id')
                                ->whereIn('bookings.status', ['confirmed', 'checked_in', 'completed'])
                                ->groupBy('type')
                                ->orderBy('total_revenue', 'desc')
                                ->get();

        return view('admin.reports', compact('monthlyBookings', 'popularRooms', 'userStats', 'revenueByRoomType'));
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Show reservations management page
     */
    public function reservations(Request $request)
    {
        try {
            $query = Booking::with(['user', 'room']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                    });
                    
                    // Check if booking_reference column exists before searching
                    if (Schema::hasColumn('bookings', 'booking_reference')) {
                        $q->orWhere('booking_reference', 'like', "%{$search}%");
                    } elseif (Schema::hasColumn('bookings', 'reference')) {
                        $q->orWhere('reference', 'like', "%{$search}%");
                    }
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Date filter - check which date column exists
            if ($request->filled('date')) {
                $dateColumn = 'check_in_date';
                if (Schema::hasColumn('bookings', 'check_in_date')) {
                    $dateColumn = 'check_in_date';
                } elseif (Schema::hasColumn('bookings', 'checkin_date')) {
                    $dateColumn = 'checkin_date';
                } elseif (Schema::hasColumn('bookings', 'check_in')) {
                    $dateColumn = 'check_in';
                } elseif (Schema::hasColumn('bookings', 'start_date')) {
                    $dateColumn = 'start_date';
                }
                
                $query->whereDate($dateColumn, $request->date);
            }

            $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

            // Calculate statistics
            $stats = [
                'total_bookings' => Booking::count(),
                'pending_bookings' => Booking::where('status', 'pending')->count(),
                'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
                'checked_in' => Booking::where('status', 'checked_in')->count(),
                'completed_bookings' => Booking::where('status', 'completed')->count(),
                'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
                'total_revenue' => 0,
                'todays_checkins' => 0,
                'todays_checkouts' => 0,
            ];

            // Calculate revenue based on available price column
            $priceColumn = 'total_price';
            if (Schema::hasColumn('bookings', 'total_price')) {
                $priceColumn = 'total_price';
            } elseif (Schema::hasColumn('bookings', 'total_amount')) {
                $priceColumn = 'total_amount';
            } elseif (Schema::hasColumn('bookings', 'price')) {
                $priceColumn = 'price';
            } elseif (Schema::hasColumn('bookings', 'amount')) {
                $priceColumn = 'amount';
            }

            $stats['total_revenue'] = Booking::where('status', 'completed')->sum($priceColumn);

            $statuses = ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled'];

            return view('admin.reservations.index', compact('bookings', 'stats', 'statuses'));

        } catch (\Exception $e) {
            // If there's any error, return empty data
            $bookings = collect([]);
            $stats = [
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
            $statuses = ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled'];

            return view('admin.reservations.index', compact('bookings', 'stats', 'statuses'))
                    ->with('error', 'Unable to load bookings data. Please check your database configuration.');
        }
    }
}