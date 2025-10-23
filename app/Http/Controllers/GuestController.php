<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    /**
     * Show the guest dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get guest's bookings
        $bookings = Booking::where('user_id', $user->id)
                          ->with(['room'])
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

        // Get booking statistics for this guest
        $stats = [
            'total_bookings' => Booking::where('user_id', $user->id)->count(),
            'pending_bookings' => Booking::where('user_id', $user->id)->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('user_id', $user->id)->where('status', 'confirmed')->count(),
            'completed_bookings' => Booking::where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        // Get available rooms count
        $available_rooms = Room::where('is_available', true)->count();

        // Get service requests count for this guest
        $service_requests_count = ServiceRequest::where('guest_id', $user->id)
                                               ->where('status', '!=', 'cancelled')
                                               ->count();

        return view('guest.dashboard', compact('bookings', 'stats', 'available_rooms', 'service_requests_count'));
    }

    /**
     * Show guest's bookings
     */
    public function bookings()
    {
        $user = auth()->user();
        
        $bookings = Booking::where('user_id', $user->id)
                          ->with(['room', 'services'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        return view('guest.bookings', compact('bookings'));
    }

    /**
     * Show specific booking details
     */
    public function showBooking(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to booking details.');
        }

        $booking->load(['room', 'services']);
        
        return view('guest.bookings.show', compact('booking'));
    }

    /**
     * Show available services
     */
    public function services()
    {
        $services = Service::where('is_available', true)
                          ->orderBy('category')
                          ->orderBy('name')
                          ->get()
                          ->groupBy('category');

        return view('guest.services', compact('services'));
    }

    /**
     * Show guest profile
     */
    public function profile()
    {
        $user = auth()->user();
        return view('guest.profile', compact('user'));
    }

    /**
     * Browse available rooms
     */
    public function browseRooms(Request $request)
    {
        try {
            // Start query with images relationship
            $query = Room::with('images');
            
            // Exclude cottage types (they should use the cottages booking system)
            $cottageTypes = ['Umbrella Cottage', 'Bahay Kubo'];
            $query->whereNotIn('type', $cottageTypes);

            // Get room types from your actual database (excluding cottages)
            $types = Room::whereNotIn('type', $cottageTypes)
                        ->distinct()
                        ->pluck('type')
                        ->filter()
                        ->values();
            if ($types->isEmpty()) {
                $types = collect(['Standard', 'suite', 'villa', 'standard', 'Suite']);
            }

            // Apply filters
            if ($request->filled('type')) {
                $query->where('type', 'like', '%' . $request->type . '%');
            }

            if ($request->filled('guests')) {
                $query->where('capacity', '>=', $request->guests);
            }

            // Only show available rooms
            $query->where('is_available', 1);

            // Get rooms with pagination
            $rooms = $query->orderBy('price', 'asc')->paginate(9);

            // Debug: Log what we found
            \Log::info('Rooms loaded with images:', [
                'total_rooms' => $rooms->count(),
                'rooms_with_images' => $rooms->filter(function($room) {
                    return $room->images->count() > 0;
                })->count()
            ]);

            // Process rooms to ensure compatibility with your view
            $rooms->getCollection()->transform(function ($room) {
                // Ensure amenities is properly decoded
                if (is_string($room->amenities)) {
                    $room->amenities = json_decode($room->amenities, true) ?: [];
                }
                
                // Ensure images relationship is always a collection
                if (!$room->relationLoaded('images')) {
                    $room->setRelation('images', collect([]));
                }
                
                // Debug each room
                \Log::info("Room {$room->id} has {$room->images->count()} images");
                
                return $room;
            });

            $amenities = collect(['WiFi', 'Air Conditioning', 'TV', 'Mini Bar', 'Ocean View', 'Private Pool']);
            $priceRanges = [
                'budget' => ['min' => 0, 'max' => 8000, 'label' => 'Budget (₱0 - ₱8,000)'],
                'standard' => ['min' => 8001, 'max' => 15000, 'label' => 'Standard (₱8,001 - ₱15,000)'],
                'premium' => ['min' => 15001, 'max' => 20000, 'label' => 'Premium (₱15,001 - ₱20,000)'],
                'luxury' => ['min' => 20001, 'max' => 999999, 'label' => 'Luxury (₱20,000+)']
            ];

            return view('guest.rooms.browse', compact('rooms', 'types', 'amenities', 'priceRanges'));

        } catch (\Exception $e) {
            \Log::error('Error in browseRooms: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $rooms = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, 9, 1,
                ['path' => request()->url(), 'pageName' => 'page']
            );
            
            $types = collect(['Standard', 'suite', 'villa']);
            $amenities = collect(['WiFi', 'Air Conditioning', 'TV']);
            $priceRanges = [];
            
            return view('guest.rooms.browse', compact('rooms', 'types', 'amenities', 'priceRanges'))
                   ->with('error', 'Unable to load rooms: ' . $e->getMessage());
        }
    }
}

