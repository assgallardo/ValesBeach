<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['service', 'assignedStaff']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('requested_at', $request->date);
        }

        $serviceRequests = $query->orderBy('requested_at', 'desc')->paginate(15);
        
        $services = Service::all();
        $statuses = ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'];
        $staff = User::where('role', 'staff')->get();

        return view('manager.service-requests.index', compact('serviceRequests', 'services', 'statuses', 'staff'));
    }

    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,assigned,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string'
        ]);

        $data = ['status' => $request->status];

        if ($request->filled('assigned_to')) {
            $data['assigned_to'] = $request->assigned_to;
        }

        if ($request->filled('notes')) {
            $data['notes'] = $request->notes;
        }

        if ($request->status === 'completed') {
            $data['completed_at'] = now();
        }

        $serviceRequest->update($data);

        return redirect()->route('manager.service-requests.index')
                        ->with('success', 'Service request updated successfully.');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['service', 'assignedStaff']);
        return view('manager.service-requests.show', compact('serviceRequest'));
    }
}