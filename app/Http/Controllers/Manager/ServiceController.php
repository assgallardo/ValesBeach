<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();

        // Category filter (same as guest)
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter (manager-specific)
        if ($request->filled('status')) {
            $isAvailable = $request->status === 'available';
            $query->where('is_available', $isAvailable);
        }

        $services = $query->orderBy('category')
                         ->orderBy('name')
                         ->paginate(12)
                         ->appends($request->query());

        return view('manager.services.index', compact('services'));
    }

    public function create()
    {
        return view('manager.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:spa,dining,activities,transportation,room_service',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'capacity' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = $imagePath;
        }

        // Ensure is_available is boolean
        $data['is_available'] = $request->has('is_available');

        Service::create($data);

        return redirect()->route('manager.services.index')
                        ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified service
     */
    public function show(Service $service)
    {
        return view('manager.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service
     */
    public function edit(Service $service)
    {
        return view('manager.services.edit', compact('service'));
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:spa,dining,transportation,activities,room_service',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'capacity' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'is_available' => 'boolean'
        ]);

        $data = $request->all();
        
        // Handle image removal
        if ($request->input('remove_image') == '1') {
            // Delete old image if exists
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = null;
        }
        // Handle image upload
        elseif ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            
            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = $imagePath;
        }

        // Ensure is_available is set correctly
        $data['is_available'] = $request->has('is_available') ? true : false;

        $service->update($data);

        return redirect()->route('manager.services.show', $service)
                         ->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified service
     */
    public function destroy(Service $service)
    {
        // Delete image if exists
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        
        $service->delete();

        return redirect()->route('manager.services.index')
                         ->with('success', 'Service deleted successfully!');
    }

    /**
     * Toggle service availability status
     */
    public function toggleStatus(Service $service)
    {
        $service->update([
            'is_available' => !$service->is_available
        ]);

        $status = $service->is_available ? 'available' : 'unavailable';
        
        return redirect()->back()->with('success', "Service '{$service->name}' marked as {$status}!");
    }
}