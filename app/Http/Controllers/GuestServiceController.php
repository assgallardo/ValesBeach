<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceRequest;

class GuestServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::where('is_available', true);

        // Filter by category if provided
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

        $services = $query->orderBy('category')
                         ->orderBy('name')
                         ->paginate(12);

        return view('guest.services.index', compact('services'));
    }

    public function show(Service $service)
    {
        // Get related services in the same category (excluding current service)
        $relatedServices = Service::where('category', $service->category)
                                 ->where('is_available', true)
                                 ->where('id', '!=', $service->id)
                                 ->limit(3)
                                 ->get();

        return view('guest.services.show', compact('service', 'relatedServices'));
    }

    public function create(Service $service)
    {
        // Check if service is available
        if (!$service->is_available) {
            return redirect()->route('guest.services.show', $service)
                           ->with('error', 'This service is currently unavailable for booking.');
        }

        return view('guest.services.request', compact('service'));
    }

    public function store(Request $request, Service $service)
    {
        // Check if service is available
        if (!$service->is_available) {
            return redirect()->route('guest.services.show', $service)
                           ->with('error', 'This service is currently unavailable for booking.');
        }

        $request->validate([
            'requested_date' => 'required|date|after_or_equal:today',
            'requested_time' => 'required',
            'guests' => 'required|integer|min:1' . ($service->capacity ? '|max:' . $service->capacity : ''),
            'special_requests' => 'nullable|string|max:1000'
        ]);

        $user = auth()->user();

        // Create the service request with both old and new structure
        ServiceRequest::create([
            'service_id' => $service->id,
            'user_id' => $user->id,
            'guest_name' => $user->name,
            'guest_email' => $user->email,
            'requested_date' => $request->requested_date,
            'requested_time' => $request->requested_time,
            'guests' => $request->guests,
            'special_requests' => $request->special_requests,
            'description' => "Guest booking for {$service->name} - {$request->guests} guests",
            'status' => 'pending',
            'priority' => 'medium',
            'requested_at' => now()
        ]);

        return redirect()->route('guest.services.show', $service)
                       ->with('success', 'Service request submitted successfully! We will contact you soon to confirm your booking.');
    }

    public function history()
    {
        $requests = ServiceRequest::where('user_id', auth()->id())
                                ->with('service')
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);
        
        return view('guest.services.history', compact('requests'));
    }

    public function cancel(ServiceRequest $serviceRequest)
    {
        // Check if user owns this request
        if ($serviceRequest->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if cancellation is allowed
        if (!$serviceRequest->canBeCancelled()) {
            return redirect()->back()->with('error', 'This booking cannot be cancelled.');
        }

        $serviceRequest->update([
            'status' => 'cancelled',
            'manager_notes' => 'Cancelled by guest on ' . now()->format('M d, Y g:i A')
        ]);

        return redirect()->route('guest.services.requests.history')
                       ->with('success', 'Booking cancelled successfully.');
    }
}
