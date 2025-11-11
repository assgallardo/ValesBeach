<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CLEAN UP DUPLICATE HOUSEKEEPING TASKS ===\n\n";

// Find all housekeeping tasks
$allTasks = \App\Models\Task::where('task_type', 'housekeeping')->get();

echo "Total housekeeping tasks found: {$allTasks->count()}\n\n";

// Group by booking_id to find duplicates
$tasksByBooking = $allTasks->groupBy('booking_id');

$duplicatesRemoved = 0;
$orphansRemoved = 0;

foreach ($tasksByBooking as $bookingId => $tasks) {
    if ($tasks->count() > 1) {
        echo "Booking #{$bookingId} has {$tasks->count()} tasks (DUPLICATES FOUND)\n";
        
        // Keep only the most recent one, delete the rest
        $sortedTasks = $tasks->sortByDesc('created_at');
        $keepTask = $sortedTasks->first();
        
        echo "  Keeping Task #{$keepTask->id} (created: {$keepTask->created_at})\n";
        
        foreach ($sortedTasks->skip(1) as $duplicateTask) {
            echo "  Deleting duplicate Task #{$duplicateTask->id} (created: {$duplicateTask->created_at})\n";
            $duplicateTask->delete();
            $duplicatesRemoved++;
        }
        echo "\n";
    }
    
    // Check if booking still exists and has checked_out status
    $booking = \App\Models\Booking::find($bookingId);
    
    if (!$booking || $booking->status !== 'checked_out') {
        $status = $booking ? $booking->status : 'DELETED';
        echo "Booking #{$bookingId} status is '{$status}' (not checked_out)\n";
        
        // Delete non-completed tasks for bookings that aren't checked_out
        foreach ($tasks as $task) {
            if ($task->status !== 'completed') {
                echo "  Removing Task #{$task->id} (status: {$task->status})\n";
                $task->delete();
                $orphansRemoved++;
            } else {
                echo "  Keeping completed Task #{$task->id}\n";
            }
        }
        echo "\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Duplicate tasks removed: {$duplicatesRemoved}\n";
echo "Orphan tasks removed: {$orphansRemoved}\n";
echo "Total tasks removed: " . ($duplicatesRemoved + $orphansRemoved) . "\n\n";

// Show remaining tasks
$remainingTasks = \App\Models\Task::where('task_type', 'housekeeping')->get();
echo "Remaining housekeeping tasks: {$remainingTasks->count()}\n";

if ($remainingTasks->count() > 0) {
    foreach ($remainingTasks as $task) {
        $booking = \App\Models\Booking::find($task->booking_id);
        $bookingStatus = $booking ? $booking->status : 'N/A';
        echo "  Task #{$task->id}: Booking #{$task->booking_id} (booking status: {$bookingStatus}, task status: {$task->status})\n";
    }
}
