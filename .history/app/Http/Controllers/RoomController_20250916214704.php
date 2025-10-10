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
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('type', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Filter by capacity
        if ($request->capacity) {
            $query->where('capacity', '>=', $request->capacity);
        }

        // Filter by price range
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by availability status
        if ($request->has('is_available')) {
            $query->where('is_available', $request->is_available);
        }

        $rooms = $query->latest()->paginate(10)->withQueryString();
        $types = Room::distinct()->pluck('type');

        return view('admin.rooms.index', compact('rooms', 'types'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'beds' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'room_images' => 'required|array|min:1|max:10',
            'room_images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            // Create room
            $room = Room::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'capacity' => $validated['capacity'],
                'beds' => $validated['beds'],
                'amenities' => isset($validated['amenities']) ? json_encode($validated['amenities']) : null,
                'is_available' => $request->has('is_available')
            ]);

            // Handle image uploads
            if ($request->hasFile('room_images')) {
                foreach ($request->file('room_images') as $index => $image) {
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
                ->with('success', 'Room created.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Could not create room.']);
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
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'beds' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
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
            ->with('success', 'Room deleted successfully.');
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