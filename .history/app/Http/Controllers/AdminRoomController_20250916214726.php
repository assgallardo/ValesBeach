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
        $validated = $request->validate([
            'number' => 'required|string|max:10|unique:rooms',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $room = Room::create($validated);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('rooms', 'public');
                    $room->images()->create(['path' => $path]);
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Room created successfully',
                    'room' => $room
                ], 201);
            }

            return redirect()->route('admin.rooms')
                ->with('success', 'Room created.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error creating room',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error creating room: ' . $e->getMessage()])
                ->withInput();
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
        $validated = $request->validate([
            'number' => 'required|string|max:10|unique:rooms,number,' . $room->id,
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $room->update($validated);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('rooms', 'public');
                    $room->images()->create(['path' => $path]);
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Room updated successfully',
                    'room' => $room
                ]);
            }

            return redirect()->route('admin.rooms')
                ->with('success', 'Room updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error updating room',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error updating room: ' . $e->getMessage()])
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
                ->with('success', 'Room deleted successfully');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error deleting room',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error deleting room: ' . $e->getMessage()]);
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

            return back()->with('success', 'Room availability updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error updating room availability',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error updating room availability: ' . $e->getMessage()]);
        }
    }
}
