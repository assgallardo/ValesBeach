<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManagerRoomController extends Controller
{
    /**
     * Display a listing of rooms for management.
     */
    public function index(Request $request)
    {
        $query = Room::query();

        // Apply search filter - search by name, type, category, or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply category filter (changed from type to category)
        if ($request->filled('category')) {
            $query->where('category', $request->category);
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
        $sortField = in_array($request->input('sort_by'), ['name', 'type', 'category', 'price', 'created_at']) 
            ? $request->input('sort_by') 
            : 'name';
            
        $sortOrder = in_array(strtolower($request->input('sort_order')), ['asc', 'desc']) 
            ? strtolower($request->input('sort_order')) 
            : 'asc';

        $query->orderBy($sortField, $sortOrder);

        // Get paginated results
        $rooms = $query->paginate(10)->withQueryString();

        return view('manager.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room.
     */
    public function create()
    {
        return view('manager.rooms.create');
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request)
    {
        \Log::info('=== MANAGER ROOM CREATION DEBUG ===');
        \Log::info('All request data:', $request->all());
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:100',
                'description' => 'required|string',
                'capacity' => 'required|integer|min:1',
                'beds' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'amenities' => 'nullable|array',
                'check_in_time' => 'nullable|date_format:H:i',
                'check_out_time' => 'nullable|date_format:H:i',
                'is_available' => 'nullable',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            \Log::info('Validation passed. Validated data:', $validated);

            // Handle checkbox and status properly
            $isAvailable = $request->has('is_available') ? true : false;

            $roomData = [
                'name' => $validated['name'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'capacity' => $validated['capacity'],
                'beds' => $validated['beds'],
                'price' => $validated['price'],
                'amenities' => $request->amenities ? json_encode($request->amenities) : null,
                'check_in_time' => $validated['check_in_time'] ?? null,
                'check_out_time' => $validated['check_out_time'] ?? null,
                'is_available' => $isAvailable,
            ];

            \Log::info('Room data to be created:', $roomData);

            // Create the room
            $room = Room::create($roomData);

            \Log::info('Room created successfully with ID: ' . $room->id);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $this->handleImageUploads($request, $room);
            }

            return redirect()->route('manager.rooms.index')
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
     * Display the specified room.
     */
    public function show(Room $room)
    {
        return view('manager.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(Room $room)
    {
        return view('manager.rooms.edit', compact('room'));
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:10|unique:rooms,number,' . $room->id,
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'beds' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'is_available' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Handle checkbox
            $validated['is_available'] = $request->has('is_available');
            
            $room->update($validated);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $imagesPath = public_path('images/rooms');
                    $image->move($imagesPath, $imageName);
                    
                    if (class_exists('App\Models\RoomImage')) {
                        $room->images()->create([
                            'image_path' => 'images/rooms/' . $imageName
                        ]);
                    }
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Room updated successfully',
                    'room' => $room
                ]);
            }

            return redirect()->route('manager.rooms.index')
                ->with('success', 'Room updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error updating room',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Could not update room.'])
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

            return redirect()->route('manager.rooms.index')
                ->with('success', 'Room deleted successfully.');
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

            return response()->json([
                'success' => true,
                'message' => 'Room availability updated successfully',
                'room' => $room
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating room availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete room image.
     */
    public function deleteImage(Request $request, Room $room)
    {
        try {
            $imageId = $request->input('image_id');
            
            if (class_exists('App\Models\RoomImage')) {
                $image = $room->images()->find($imageId);
                
                if ($image) {
                    // Delete physical file
                    $imagePath = public_path($image->image_path);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    
                    // Delete database record
                    $image->delete();
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Image deleted successfully'
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting image',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

