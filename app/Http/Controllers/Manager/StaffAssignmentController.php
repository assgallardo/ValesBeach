<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;

class StaffAssignmentController extends Controller
{
    /**
     * Display a listing of service requests.
     */
    public function index()
    {
        $availableStaff = User::where('role', 'staff')
            ->where('status', 'active')
            ->get();

        // Get available services (removed status filter since column doesn't exist)
        $availableServices = \App\Models\Service::orderBy('name')->get();

        // Get service requests with relationships, excluding cancelled ones
        $serviceRequests = ServiceRequest::with(['assignedTo', 'guest', 'room', 'service'])
            ->active() // Use the scope to exclude cancelled
            ->orderBy('deadline', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get counts for statistics (excluding cancelled)
        $pendingRequests = ServiceRequest::active()->whereIn('status', ['pending', 'confirmed'])->count();
        $assignedRequests = ServiceRequest::active()->whereIn('status', ['assigned', 'in_progress'])->count();
        $completedRequests = ServiceRequest::active()->where('status', 'completed')
            ->whereDate('updated_at', today())
            ->count();
        $overdueRequests = ServiceRequest::active()->where('deadline', '<', now())->whereNotIn('status', ['completed', 'cancelled'])->count();

        return view('manager.staff-assignment.index', compact(
            'serviceRequests',
            'availableStaff', 
            'availableServices',
            'pendingRequests',
            'assignedRequests',
            'completedRequests',
            'overdueRequests'
        ));
    }

    /**
     * Show the form for editing the specified service request.
     */
    public function edit(ServiceRequest $serviceRequest)
    {
        $availableStaff = User::where('role', 'staff')
            ->where('status', 'active')
            ->get();

        // Get available services (removed status filter)
        $availableServices = \App\Models\Service::orderBy('name')->get();

        return view('manager.staff-assignment.edit', compact(
            'serviceRequest', 
            'availableStaff',
            'availableServices'
        ));
    }

    /**
     * Update a service request
     */
    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'service_type' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'nullable|date|after:now',
            'estimated_duration' => 'nullable|integer|min:15|max:480',
            'status' => 'required|in:pending,confirmed,assigned,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'manager_notes' => 'nullable|string|max:1000',
            'guests_count' => 'nullable|integer|min:1',
            'special_requests' => 'nullable|string|max:500',
            'preferred_date' => 'nullable|date',
            'preferred_time' => 'nullable'
        ]);

        // Update the service request
        $serviceRequest->update([
            'service_type' => $request->service_type,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'estimated_duration' => $request->estimated_duration,
            'status' => $request->status,
            'assigned_to' => $request->assigned_to,
            'manager_notes' => $request->manager_notes,
            'guests_count' => $request->guests_count,
            'special_requests' => $request->special_requests,
            'preferred_date' => $request->preferred_date,
            'preferred_time' => $request->preferred_time,
        ]);

        // If status changed to assigned and we have an assignee
        if ($request->status === 'assigned' && $request->assigned_to) {
            $serviceRequest->update(['assigned_at' => now()]);
            
            // Create or update related task
            $this->createOrUpdateTask($serviceRequest, $request->assigned_to);
        }

        // If status changed to completed
        if ($request->status === 'completed') {
            $serviceRequest->update(['completed_at' => now()]);
        }

        return redirect()->route('manager.staff-assignment.index')
            ->with('success', 'Service request updated successfully!');
    }

    /**
     * Show quick edit modal
     */
    public function quickEdit(ServiceRequest $serviceRequest)
    {
        $availableStaff = User::where('role', 'staff')
            ->where('status', 'active')
            ->get();

        return response()->json([
            'serviceRequest' => $serviceRequest->load(['assignedTo', 'guest', 'room']),
            'availableStaff' => $availableStaff
        ]);
    }

    /**
     * Quick update a service request
     */
    public function quickUpdate(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
            'deadline' => 'nullable|date',
            'estimated_duration' => 'nullable|integer|min:15|max:480',
            'status' => 'nullable|in:pending,confirmed,assigned,in_progress,completed,cancelled',
            'manager_notes' => 'nullable|string|max:500',
            'service_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'guests_count' => 'nullable|integer|min:1'
        ]);

        $updateData = $request->only([
            'assigned_to', 'deadline', 'estimated_duration', 'status', 'manager_notes',
            'service_type', 'description', 'guests_count'
        ]);

        // Remove null values
        $updateData = array_filter($updateData, function($value) {
            return $value !== null && $value !== '';
        });

        $serviceRequest->update($updateData);

        // Handle assignment logic
        if ($request->has('assigned_to') && $request->assigned_to) {
            $serviceRequest->update(['assigned_at' => now()]);
            
            // Create or update task when assigning to staff
            $this->createOrUpdateTask($serviceRequest, $request->assigned_to);
            
            // Set status to assigned if not already set
            if (!$request->has('status') || !$request->status) {
                $serviceRequest->update(['status' => 'assigned']);
            }
        } elseif ($request->has('assigned_to') && !$request->assigned_to) {
            // Unassigning - remove task and reset status
            if ($serviceRequest->task) {
                $serviceRequest->task->update(['status' => 'cancelled']);
            }
            $serviceRequest->update([
                'assigned_at' => null,
                'status' => 'confirmed'
            ]);
        }

        // Update completion timestamp
        if ($request->has('status') && $request->status === 'completed') {
            $serviceRequest->update(['completed_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service request updated successfully!',
            'serviceRequest' => $serviceRequest->fresh()->load(['assignedTo', 'task'])
        ]);
    }

    /**
     * Assign a service request to a staff member.
     */
    public function assign(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'deadline' => 'nullable|date|after:now',
            'estimated_duration' => 'nullable|integer|min:15|max:480',
            'notes' => 'nullable|string|max:500'
        ]);

        $staff = User::findOrFail($request->assigned_to);

        $serviceRequest->update([
            'assigned_to' => $staff->id,
            'status' => 'assigned',
            'deadline' => $request->deadline,
            'estimated_duration' => $request->estimated_duration,
            'manager_notes' => $request->notes,
            'assigned_at' => now()
        ]);

        // Create task for the staff member
        $this->createOrUpdateTask($serviceRequest, $staff->id);

        return redirect()->back()->with('success', "Service request assigned to {$staff->name} successfully!");
    }

    /**
     * Remove assignment from service request.
     */
    public function unassign(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update([
            'assigned_to' => null,
            'status' => 'confirmed',
            'assigned_at' => null
        ]);

        // Cancel related task if exists
        if ($serviceRequest->task) {
            $serviceRequest->task->update(['status' => 'cancelled']);
        }

        return redirect()->back()->with('success', 'Assignment removed successfully!');
    }

    /**
     * Update status of a service request
     */
    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,assigned,in_progress,completed,cancelled'
        ]);

        $serviceRequest->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null
        ]);

        // Update related task status
        if ($serviceRequest->task) {
            $taskStatus = $request->status === 'completed' ? 'completed' : 
                         ($request->status === 'cancelled' ? 'cancelled' : 'pending');
            $serviceRequest->task->update(['status' => $taskStatus]);
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }

    /**
     * Confirm task assignment
     */
    public function confirmTask(ServiceRequest $serviceRequest)
    {
        // Check if the service request is assigned to someone
        if (!$serviceRequest->assigned_to) {
            return response()->json([
                'success' => false,
                'message' => 'No staff member assigned to this service request'
            ], 400);
        }

        // Update service request status to confirmed
        $serviceRequest->update([
            'status' => 'confirmed'
        ]);

        // Update or create task and set it to confirmed
        if ($serviceRequest->task) {
            $serviceRequest->task->update(['status' => 'confirmed']);
        } else {
            // Create task if it doesn't exist
            $this->createOrUpdateTask($serviceRequest, $serviceRequest->assigned_to);
            $serviceRequest->task->update(['status' => 'confirmed']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task confirmed successfully! Staff member has been notified.'
        ]);
    }

    /**
     * Cancel a service request (soft delete)
     */
    public function cancel(ServiceRequest $serviceRequest)
    {
        // Cancel related task if exists
        if ($serviceRequest->task) {
            $serviceRequest->task->update(['status' => 'cancelled']);
        }

        // Mark as cancelled and hide from main list
        $serviceRequest->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'assigned_to' => null
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Service request cancelled successfully!'
        ]);
    }

    /**
     * Permanently delete a service request
     */
    public function destroy(ServiceRequest $serviceRequest)
    {
        // Cancel related task if exists
        if ($serviceRequest->task) {
            $serviceRequest->task->delete();
        }

        $serviceRequest->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Service request deleted permanently!'
        ]);
    }

    /**
     * Bulk assign multiple requests.
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'service_requests' => 'required|array',
            'service_requests.*' => 'exists:service_requests,id',
            'assigned_to' => 'required|exists:users,id',
            'deadline' => 'nullable|date|after:now',
            'estimated_duration' => 'nullable|integer|min:15|max:480'
        ]);

        $staff = User::findOrFail($request->assigned_to);
        $assignedCount = 0;

        foreach ($request->service_requests as $requestId) {
            $serviceRequest = ServiceRequest::find($requestId);
            
            if ($serviceRequest && in_array($serviceRequest->status, ['pending', 'confirmed'])) {
                $serviceRequest->update([
                    'assigned_to' => $staff->id,
                    'status' => 'assigned',
                    'deadline' => $request->deadline,
                    'estimated_duration' => $request->estimated_duration,
                    'assigned_at' => now()
                ]);

                $this->createOrUpdateTask($serviceRequest, $staff->id);
                $assignedCount++;
            }
        }

        return redirect()->back()->with('success', "{$assignedCount} service requests assigned to {$staff->name} successfully!");
    }

    /**
     * Create or update task for service request
     */
    private function createOrUpdateTask($serviceRequest, $staffId)
    {
        // Create or update task
        \App\Models\Task::updateOrCreate(
            ['service_request_id' => $serviceRequest->id],
            [
                'title' => 'Service Request: ' . $serviceRequest->service_type,
                'description' => $serviceRequest->description . 
                               ($serviceRequest->manager_notes ? "\n\nManager Notes: " . $serviceRequest->manager_notes : ''),
                'assigned_to' => $staffId,
                'assigned_by' => auth()->id(),
                'status' => 'pending',
                'due_date' => $serviceRequest->deadline ?? now()->addHours(24)
            ]
        );
    }
}
