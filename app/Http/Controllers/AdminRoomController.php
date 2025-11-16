<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminRoomController extends Controller
{
    /**
     * Display a listing of rooms for management.
     */
    public function index(Request $request)
    {
        $query = Room::query();

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_available', $request->status === 'available');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortField = in_array($request->input('sort_by'), ['number', 'name', 'type', 'price', 'created_at']) 
            ? $request->input('sort_by') 
            : 'number';
            
        $sortOrder = in_array(strtolower($request->input('sort_order')), ['asc', 'desc']) 
            ? strtolower($request->input('sort_order')) 
            : 'asc';

        $query->orderBy($sortField, $sortOrder);

        // Get paginated results
        $rooms = $query->paginate(10)->withQueryString();

        // Get available types for filter dropdown
        $types = Room::distinct()->pluck('type');

        return view('admin.rooms.index', [
            'rooms' => $rooms,
            'types' => $types,
            'currentType' => $request->type,
            'currentStatus' => $request->status,
            'currentSearch' => $request->search,
            'currentSortField' => $sortField,
            'currentSortOrder' => $sortOrder,
        ]);
    }

    /**
     * Show the form for creating a new room.
     */
    public function create()
    {
        return view('admin.rooms.create');
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request)
    {
        \Log::info('=== ROOM CREATION DEBUG ===');
        \Log::info('All request data:', $request->all());
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'key_number' => 'nullable|string|max:20',
                'category' => 'nullable|string|max:50',
                'type' => 'required|string|max:100',
                'description' => 'required|string',
                'capacity' => 'required|integer|min:1',
                'beds' => 'nullable|integer|min:0',
                'price' => 'required|numeric|min:0',
                'check_in_time' => 'nullable|date_format:H:i,H:i:s',
                'check_out_time' => 'nullable|date_format:H:i,H:i:s',
                'amenities' => 'nullable|array',
                'is_available' => 'nullable',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            \Log::info('Validation passed. Validated data:', $validated);

            // Handle checkbox and status properly
            $isAvailable = $request->has('is_available') ? true : false;
            $status = $isAvailable ? 'available' : 'unavailable';

            $roomData = [
                'name' => $validated['name'],
                'key_number' => $validated['key_number'] ?? null,
                'category' => $validated['category'] ?? 'Rooms',
                'type' => $validated['type'],
                'description' => $validated['description'],
                'capacity' => $validated['capacity'],
                'beds' => $validated['beds'] ?? 0,
                'price' => $validated['price'],
                'check_in_time' => $validated['check_in_time'] ?? null,
                'check_out_time' => $validated['check_out_time'] ?? null,
                'amenities' => $request->amenities ? json_encode($request->amenities) : null,
                'is_available' => $isAvailable,
                // 'status' => $status  // COMMENTED OUT TEMPORARILY
            ];

            \Log::info('Room data to be created:', $roomData);

            // Create the room
            $room = Room::create($roomData);

            \Log::info('Room created successfully with ID: ' . $room->id);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $this->handleImageUploads($request, $room);
            }

            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Room creation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withErrors(['error' => 'Could not create room. Detailed error: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    private function handleImageUploads(Request $request, Room $room)
    {
        try {
            $imagesPath = public_path('images/rooms');
            if (!file_exists($imagesPath)) {
                mkdir($imagesPath, 0755, true);
            }

            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($imagesPath, $imageName);
                
                // Only create image record if RoomImage model exists
                if (class_exists('App\Models\RoomImage')) {
                    $room->images()->create([
                        'image_path' => 'images/rooms/' . $imageName
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage());
            // Don't fail the entire room creation for image issues
        }
    }

    /**
     * Show room details for editing.
     */
    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, Room $room)
    {
        \Log::info('=== ROOM UPDATE DEBUG ===');
        \Log::info('Room ID: ' . $room->id);
        \Log::info('All request data:', $request->all());
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key_number' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:50',
            'type' => 'required|string|max:50',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'beds' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'check_in_time' => 'nullable|date_format:H:i,H:i:s',
            'check_out_time' => 'nullable|date_format:H:i,H:i:s',
            'is_available' => 'nullable',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        \Log::info('Validation passed. Validated data:', $validated);

        try {
            // Handle checkbox
            $validated['is_available'] = $request->has('is_available');
            
            \Log::info('Data to update:', $validated);
            
            $room->update($validated);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('rooms', 'public');
                    $room->images()->create(['path' => $path]);
                }
            }

            \Log::info('Room updated successfully');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Room updated successfully',
                    'room' => $room
                ]);
            }

            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Room update failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error updating room',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Could not update room. Error: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(Request $request, Room $room)
    {
        try {
            // Check if room has any active bookings
            if ($room->bookings()->where('status', '!=', 'cancelled')->exists()) {
                throw new \Exception('Cannot delete room with active bookings');
            }

            $room->delete();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Room deleted successfully'
                ]);
            }

            return redirect()->route('admin.rooms')
                ->with('success', 'Room deleted.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error deleting room',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Could not delete room.']);
        }
    }

    /**
     * Toggle room availability.
     */
    public function toggleAvailability(Request $request, Room $room)
    {
        try {
            $room->update(['is_available' => !$room->is_available]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Room availability updated successfully',
                    'room' => $room
                ]);
            }

            return back()->with('success', 'Availability updated.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error updating room availability',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Could not update availability.']);
        }
    }
}
