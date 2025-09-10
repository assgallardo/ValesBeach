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
                ->with('success', 'Room created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create room. ' . $e->getMessage()]);
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
                return back()->withErrors(['images' => 'Maximum 10 images allowed']);
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
            ->with('success', 'Room updated successfully');
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
        return view('admin.rooms.show', compact('room'));
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
}