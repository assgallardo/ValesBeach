<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\HousekeepingRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HousekeepingController extends Controller
{
    /**
     * Display a listing of housekeeping requests.
     */
    public function index(Request $request)
    {
        $query = HousekeepingRequest::with(['booking.user', 'room', 'assignedTo']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('triggered_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('triggered_at', '<=', $request->date_to);
        }

        // Order by priority and date
        $requests = $query->orderByRaw("
            CASE priority
                WHEN 'urgent' THEN 1
                WHEN 'high' THEN 2
                WHEN 'normal' THEN 3
                WHEN 'low' THEN 4
            END
        ")
                         ->orderBy('triggered_at', 'desc')
                         ->paginate(20);

        // Get statistics
        $stats = [
            'total' => HousekeepingRequest::count(),
            'pending' => HousekeepingRequest::pending()->count(),
            'assigned' => HousekeepingRequest::assigned()->count(),
            'in_progress' => HousekeepingRequest::inProgress()->count(),
            'completed_today' => HousekeepingRequest::completed()
                ->whereDate('completed_at', Carbon::today())
                ->count(),
        ];

        // Get staff members for assignment
        $staff = User::where('role', 'staff')->get();

        return view('manager.housekeeping.index', compact('requests', 'stats', 'staff'));
    }

    /**
     * Assign a housekeeping request to a staff member.
     */
    public function assign(Request $request, HousekeepingRequest $housekeeping)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $housekeeping->update([
            'assigned_to' => $request->assigned_to,
            'status' => HousekeepingRequest::STATUS_ASSIGNED,
            'assigned_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Housekeeping request assigned successfully.');
    }

    /**
     * Update the status of a housekeeping request.
     */
    public function updateStatus(Request $request, HousekeepingRequest $housekeeping)
    {
        $request->validate([
            'status' => 'required|in:pending,assigned,in_progress,completed,cancelled',
        ]);

        $updateData = ['status' => $request->status];

        // Set timestamps based on status
        if ($request->status === HousekeepingRequest::STATUS_IN_PROGRESS && !$housekeeping->started_at) {
            $updateData['started_at'] = now();
        }

        if ($request->status === HousekeepingRequest::STATUS_COMPLETED && !$housekeeping->completed_at) {
            $updateData['completed_at'] = now();
            if ($request->has('completion_notes')) {
                $updateData['completion_notes'] = $request->completion_notes;
            }
        }

        $housekeeping->update($updateData);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    /**
     * Update priority of a housekeeping request.
     */
    public function updatePriority(Request $request, HousekeepingRequest $housekeeping)
    {
        $request->validate([
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        $housekeeping->update(['priority' => $request->priority]);

        return redirect()->back()->with('success', 'Priority updated successfully.');
    }

    /**
     * Add notes to a housekeeping request.
     */
    public function addNotes(Request $request, HousekeepingRequest $housekeeping)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);

        $existingNotes = $housekeeping->notes ? $housekeeping->notes . "\n\n" : '';
        $newNotes = $existingNotes . '[' . now()->format('Y-m-d H:i') . '] ' . auth()->user()->name . ': ' . $request->notes;

        $housekeeping->update(['notes' => $newNotes]);

        return redirect()->back()->with('success', 'Notes added successfully.');
    }

    /**
     * Delete a housekeeping request.
     */
    public function destroy(HousekeepingRequest $housekeeping)
    {
        $housekeeping->delete();

        return redirect()->back()->with('success', 'Housekeeping request deleted successfully.');
    }
}
