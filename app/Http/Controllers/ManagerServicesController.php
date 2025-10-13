<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManagerServicesController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index(Request $request)
    {
        $query = Service::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        // Filter by availability
        if ($request->filled('status')) {
            $query->where('is_available', $request->get('status') === 'available');
        }

        $services = $query->orderBy('name')->paginate(12);
        
        return view('manager.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('manager.services.create');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'capacity' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_available'] = $request->has('is_available');

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        Service::create($data);

        return redirect()->route('manager.services.index')
                        ->with('success', 'Service created successfully!');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        return view('manager.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        return view('manager.services.edit', compact('service'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'capacity' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_available'] = $request->has('is_available');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return redirect()->route('manager.services.index')
                        ->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified service from storage.
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
     * Toggle service availability.
     */
    public function toggleStatus(Service $service)
    {
        $service->update([
            'is_available' => !$service->is_available
        ]);

        $status = $service->is_available ? 'enabled' : 'disabled';

        return redirect()->back()->with('success', "Service has been {$status} successfully!");
    }
}
