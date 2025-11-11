<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== HOUSEKEEPING TASKS DEBUG ===\n\n";

// Check total tasks
$totalTasks = \App\Models\Task::count();
echo "Total Tasks in database: $totalTasks\n";

// Check housekeeping tasks
$housekeepingTasks = \App\Models\Task::where('task_type', 'housekeeping')->get();
echo "Total Housekeeping Tasks: " . $housekeepingTasks->count() . "\n\n";

// Display housekeeping tasks
if ($housekeepingTasks->count() > 0) {
    echo "Housekeeping Tasks Details:\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($housekeepingTasks as $task) {
        echo "Task ID: {$task->id}\n";
        echo "Title: {$task->title}\n";
        echo "Task Type: {$task->task_type}\n";
        echo "Status: {$task->status}\n";
        echo "Booking ID: {$task->booking_id}\n";
        echo "Assigned To: " . ($task->assigned_to ? "User #{$task->assigned_to}" : "Unassigned") . "\n";
        echo "Due Date: " . ($task->due_date ? $task->due_date->format('Y-m-d H:i:s') : "N/A") . "\n";
        echo "Created At: {$task->created_at->format('Y-m-d H:i:s')}\n";
        
        // Check if booking exists
        if ($task->booking_id) {
            $booking = \App\Models\Booking::find($task->booking_id);
            if ($booking) {
                echo "Booking Status: {$booking->status}\n";
                echo "Room: " . ($booking->room ? $booking->room->name : "N/A") . "\n";
                echo "Guest: " . ($booking->user ? $booking->user->name : "N/A") . "\n";
            } else {
                echo "WARNING: Booking #{$task->booking_id} not found!\n";
            }
        }
        
        echo str_repeat("-", 80) . "\n";
    }
} else {
    echo "No housekeeping tasks found in the database.\n\n";
    
    // Check recent check-outs
    $recentCheckouts = \App\Models\Booking::where('status', 'checked_out')
        ->latest()
        ->limit(5)
        ->get();
    
    echo "Recent Checked-Out Bookings:\n";
    echo str_repeat("-", 80) . "\n";
    
    if ($recentCheckouts->count() > 0) {
        foreach ($recentCheckouts as $booking) {
            echo "Booking ID: {$booking->id}\n";
            echo "Status: {$booking->status}\n";
            echo "Room: " . ($booking->room ? $booking->room->name : "N/A") . "\n";
            echo "Guest: " . ($booking->user ? $booking->user->name : "N/A") . "\n";
            echo "Updated At: {$booking->updated_at->format('Y-m-d H:i:s')}\n";
            
            // Check if there's a housekeeping request
            $housekeepingRequest = \App\Models\HousekeepingRequest::where('booking_id', $booking->id)->first();
            if ($housekeepingRequest) {
                echo "Housekeeping Request: YES (ID: {$housekeepingRequest->id})\n";
            } else {
                echo "Housekeeping Request: NO\n";
            }
            
            // Check if there's a task
            $task = \App\Models\Task::where('booking_id', $booking->id)->first();
            if ($task) {
                echo "Task: YES (ID: {$task->id}, Type: {$task->task_type})\n";
            } else {
                echo "Task: NO - MISSING!\n";
            }
            
            echo str_repeat("-", 80) . "\n";
        }
    } else {
        echo "No checked-out bookings found.\n";
    }
}

echo "\n=== Query that StaffAssignmentController uses ===\n";
$controllerQuery = \App\Models\Task::with(['assignedTo', 'assignedBy', 'booking.user', 'booking.room'])
    ->where('task_type', 'housekeeping')
    ->whereIn('status', ['pending', 'assigned', 'in_progress'])
    ->orderBy('due_date', 'asc')
    ->orderBy('created_at', 'desc')
    ->get();

echo "Results: " . $controllerQuery->count() . " tasks\n";

if ($controllerQuery->count() > 0) {
    echo "\nTasks that should appear in Task Assignment:\n";
    foreach ($controllerQuery as $task) {
        echo "  - Task #{$task->id}: {$task->title} (Status: {$task->status})\n";
    }
}
