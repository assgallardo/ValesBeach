<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\RoomImage;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query();

        // Search by name, type, or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by capacity
        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', $request->capacity);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by availability status
        if ($request->filled('is_available')) {
            $query->where('is_available', $request->is_available);
        }

        $rooms = $query->latest()->paginate(10)->withQueryString();

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        // Add logging for debugging
        \Log::info('Room creation attempt:', $request->all());
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key_number' => 'nullable|string|max:20',
            'type' => 'required|string|max:255',
            'category' => 'required|in:Rooms,Cottages,Event and Dining',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'beds' => 'required|integer|min:0',
            'amenities' => 'nullable|array',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'images' => 'nullable|array|max:10',        // CHANGED FROM room_images
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'  // CHANGED FROM room_images.*
        ]);

        try {
            // Create room
            $room = Room::create([
                'name' => $validated['name'],
                'key_number' => $validated['key_number'] ?? null,
                'type' => $validated['type'],
                'category' => $validated['category'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'capacity' => $validated['capacity'],
                'beds' => $validated['beds'],
                'amenities' => isset($validated['amenities']) ? json_encode($validated['amenities']) : null,
                'check_in_time' => $validated['check_in_time'] ?? null,
                'check_out_time' => $validated['check_out_time'] ?? null,
                'is_available' => $request->has('is_available')
            ]);

            \Log::info('Room created successfully:', ['room_id' => $room->id]);

            // Handle image uploads - CHANGED FROM room_images to images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('rooms', 'public');
                    $room->images()->create([
                        'image_path' => $path,
                        'is_featured' => $index === 0, // First image is featured
                        'display_order' => $index
                    ]);
                }
            }

            return redirect()
                ->route('admin.rooms.index')
                ->with('success', 'Room created successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Room creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Could not create room. Error: ' . $e->getMessage()]);
        }
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key_number' => 'nullable|string|max:20',
            'type' => 'required|string|max:255',
            'category' => 'required|in:Rooms,Cottages,Event and Dining',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'beds' => 'required|integer|min:0',
            'amenities' => 'nullable|array',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'room_images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Handle amenities array
        if (isset($validated['amenities'])) {
            $validated['amenities'] = json_encode($validated['amenities']);
        }

        // Set availability
        $validated['is_available'] = $request->has('is_available');

        $room->update($validated);

        if ($request->hasFile('room_images')) {
            $currentCount = $room->images()->count();
            $newImages = count($request->file('room_images'));
            
            if (($currentCount + $newImages) > 10) {
                return back()->withErrors(['images' => 'Maximum 10 images allowed.']);
            }

            foreach ($request->file('room_images') as $index => $image) {
                $path = $image->store('rooms', 'public');
                $room->images()->create([
                    'image_path' => $path,
                    'display_order' => $currentCount + $index
                ]);
            }
        }

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Room updated.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Room deleted.');
    }

    public function toggleAvailability(Room $room): JsonResponse
    {
        $room->update([
            'is_available' => !$room->is_available
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room availability updated successfully'
        ]);
    }

    public function show(Room $room)
    {
        // Load room images ordered by display order
        $room->load(['images' => function($query) {
            $query->orderBy('display_order');
        }]);
        
        // Check if room is available
        $isAvailable = $room->is_available;

        // Get upcoming bookings for availability calendar
        $upcomingBookings = $room->bookings()
            ->where('check_out', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->get(['check_in', 'check_out']);

        // Determine which view to show based on user role
        $user = auth()->user();
        if ($user && in_array($user->role, ['admin', 'manager', 'staff'])) {
            // Show admin view for admin, manager, and staff
            return view('admin.rooms.show', compact('room'));
        }
        
        // Show guest view for guests or unauthenticated users
        return view('guest.rooms.show', compact('room', 'isAvailable', 'upcomingBookings'));
    }

    // Add method to handle image deletion
    public function deleteImage($roomId, $imageId)
    {
        $image = RoomImage::findOrFail($imageId);
        if ($image->room_id != $roomId) {
            abort(403);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function browse(Request $request)
    {
        $query = Room::query()->with('images')->where('is_available', true);

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        $rooms = $query->latest()->paginate(9);
        $types = Room::distinct()->pluck('type');

        return view('guest.rooms.browse', compact('rooms', 'types'));
    }

    // Add method to handle booking form
    public function showBookingForm(Room $room)
    {
        return view('guest.rooms.booking-form', compact('room'));
    }

    /**
     * Display rooms list for staff
     */
    public function staffIndex()
    {
        $rooms = Room::with('images')
            ->latest()
            ->paginate(10);

        return view('staff.rooms.index', compact('rooms'));
    }

    /**
     * Display room details for staff
     */
    public function staffShow(Room $room)
    {
        $room->load('images');
        return view('staff.rooms.show', compact('room'));
    }
}