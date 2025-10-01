<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        // Get services without the service_requests relationship for now
        $services = Service::all()->groupBy('category');

        // Empty array for recent requests since table doesn't exist yet
        $recentRequests = collect([]);

        return view('manager.services.index', compact('services', 'recentRequests'));
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

    public function show(Service $service)
    {
        // Don't load service requests for now
        return view('manager.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('manager.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
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
            // Delete old image if exists
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            
            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = $imagePath;
        }

        // Ensure is_available is boolean
        $data['is_available'] = $request->has('is_available');

        $service->update($data);

        return redirect()->route('manager.services.index')
                        ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        // Delete associated image
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()->route('manager.services.index')
                        ->with('success', 'Service deleted successfully.');
    }
}