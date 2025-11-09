<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class StaffTaskController extends Controller
{
    /**
     * Display a listing of tasks assigned to the current staff member.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get tasks assigned to this staff member
        $tasks = Task::with(['assignedBy', 'serviceRequest.guest'])
            ->forUser($user->id)
            ->active()
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get task statistics
        $pendingTasks = Task::forUser($user->id)->where('status', 'pending')->count();
        $inProgressTasks = Task::forUser($user->id)->where('status', 'in_progress')->count();
        $completedTasks = Task::forUser($user->id)->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count();
        $overdueTasks = Task::forUser($user->id)->overdue()->count();

        return view('staff.tasks.index', compact(
            'tasks',
            'pendingTasks',
            'inProgressTasks', 
            'completedTasks',
            'overdueTasks'
        ));
    }

    /**
     * Update the status of a task
     */
    public function updateStatus(Request $request, Task $task)
    {
        // Ensure the task belongs to the current user
        if ($task->assigned_to !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to task'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,assigned,in_progress,completed'
        ]);

        $updateData = ['status' => $request->status];
        
        // Set completion timestamp if marking as completed
        if ($request->status === 'completed') {
            $updateData['completed_at'] = now();
        } else {
            $updateData['completed_at'] = null;
        }

        $task->update($updateData);

        // Also update the related service request status if it exists
        if ($task->serviceRequest) {
            $serviceRequest = $task->serviceRequest;
            $serviceRequest->update([
                'status' => $request->status,
                'completed_at' => $request->status === 'completed' ? now() : null
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully!'
        ]);
    }

    /**
     * Add notes to a task
     */
    public function updateNotes(Request $request, Task $task)
    {
        // Ensure the task belongs to the current user
        if ($task->assigned_to !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to task'
            ], 403);
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        $task->update(['notes' => $request->notes]);

        return response()->json([
            'success' => true,
            'message' => 'Task notes updated successfully!'
        ]);
    }

    /**
     * Cancel a task
     */
    public function cancel(Request $request, Task $task)
    {
        // Ensure the task belongs to the current user
        if ($task->assigned_to !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to task'
            ], 403);
        }

        // Check if task can be cancelled
        if (in_array($task->status, ['completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a task that is already completed or cancelled'
            ], 400);
        }

        $task->update([
            'status' => 'cancelled',
            'completed_at' => now()
        ]);

        // Also update the related service request status if it exists
        if ($task->serviceRequest) {
            $serviceRequest = $task->serviceRequest;
            $serviceRequest->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task cancelled successfully!'
        ]);
    }

    /**
     * Get task details for modal display
     */
    public function show(Task $task)
    {
        // Ensure the task belongs to the current user
        if ($task->assigned_to !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to task'
            ], 403);
        }

        $task->load(['assignedBy', 'serviceRequest.guest']);

        return response()->json([
            'success' => true,
            'task' => $task
        ]);
    }
}