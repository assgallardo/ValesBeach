<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;
use App\Models\Booking;

echo "=== CLEANUP DUPLICATE HOUSEKEEPING TASKS ===\n\n";

// Get all bookings with housekeeping tasks
$bookingsWithTasks = Task::where('task_type', 'housekeeping')
    ->select('booking_id')
    ->groupBy('booking_id')
    ->havingRaw('COUNT(*) > 1')
    ->pluck('booking_id');

if ($bookingsWithTasks->isEmpty()) {
    echo "✅ No duplicate tasks found!\n";
} else {
    echo "Found bookings with duplicate tasks: " . $bookingsWithTasks->count() . "\n\n";
    
    $totalDeleted = 0;
    
    foreach ($bookingsWithTasks as $bookingId) {
        $tasks = Task::where('booking_id', $bookingId)
            ->where('task_type', 'housekeeping')
            ->orderBy('created_at', 'desc')
            ->get();
        
        echo "Booking #{$bookingId} has {$tasks->count()} housekeeping tasks:\n";
        
        // Keep the most recent task, delete the rest
        $taskToKeep = $tasks->first();
        $tasksToDelete = $tasks->skip(1);
        
        echo "  ✅ KEEPING: Task #{$taskToKeep->id} (created: {$taskToKeep->created_at}, status: {$taskToKeep->status})\n";
        
        foreach ($tasksToDelete as $task) {
            echo "  ❌ DELETING: Task #{$task->id} (created: {$task->created_at}, status: {$task->status})\n";
            $task->delete();
            $totalDeleted++;
        }
        
        echo "\n";
    }
    
    echo "=== CLEANUP COMPLETE ===\n";
    echo "Total duplicate tasks deleted: {$totalDeleted}\n";
}

echo "\n=== FINAL CHECK ===\n";

// Show remaining tasks per booking
$remainingTasks = Task::where('task_type', 'housekeeping')
    ->with('booking.room', 'booking.user')
    ->get()
    ->groupBy('booking_id');

echo "Bookings with housekeeping tasks: " . $remainingTasks->count() . "\n\n";

foreach ($remainingTasks as $bookingId => $tasks) {
    $booking = $tasks->first()->booking;
    echo "Booking #{$bookingId}:\n";
    echo "  Room: {$booking->room->name}\n";
    echo "  Guest: {$booking->user->name}\n";
    echo "  Booking Status: {$booking->status}\n";
    echo "  Housekeeping Tasks: {$tasks->count()}\n";
    
    foreach ($tasks as $task) {
        echo "    - Task #{$task->id}: {$task->status} (created: {$task->created_at})\n";
    }
    echo "\n";
}

echo "✅ Database cleanup complete!\n";
