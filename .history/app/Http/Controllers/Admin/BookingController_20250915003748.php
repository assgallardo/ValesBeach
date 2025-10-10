<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class BookingController extends Controller
{
    /**
     * Display a specific booking.
     */
    public function show(Booking $booking)
    {
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Display a listing of the bookings.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'room'])
            ->latest();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->where('check_in', '>=', Carbon::parse($request->date_from));
        }
        if ($request->filled('date_to')) {
            $query->where('check_out', '<=', Carbon::parse($request->date_to));
        }

                $bookings = $query->paginate(10)->withQueryString();
        $statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];

        return view('admin.bookings.index', compact('bookings', 'statuses'));
    }

    /**
     * Update the status of a booking.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validStatuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];
        
        $request->validate([
            'status' => 'required|in:' . implode(',', $validStatuses)
        ]);

        $booking->update([
            'status' => $request->status
        ]);

        Session::flash('success', 'Booking status has been updated successfully.');
        return redirect()->back();
    }
```
        $statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];

        return view('admin.bookings.index', compact('bookings', 'statuses'));
    }

    /**
     * Update the status of a booking.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,confirmed,checked_in,checked_out,cancelled'],
        ]);

        $booking->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Booking status updated successfully.');
    }
}
