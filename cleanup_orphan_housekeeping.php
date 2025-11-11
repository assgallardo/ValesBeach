<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;
use App\Models\Booking;

echo "=== CLEANUP ORPHAN HOUSEKEEPING TASKS ===\n\n";

// Find housekeeping tasks where booking status is NOT checked_out or completed
$orphanTasks = Task::where('task_type', 'housekeeping')
    ->whereHas('booking', function($query) {
        $query->whereNotIn('status', ['checked_out', 'completed']);
    })
    ->with('booking')
    ->get();

if ($orphanTasks->count() === 0) {
    echo "No orphan tasks found. Database is clean!\n";
} else {
    echo "Found {$orphanTasks->count()} orphan housekeeping task(s):\n\n";
    
    foreach ($orphanTasks as $task) {
        echo "Task ID: {$task->id}\n";
        echo "  Booking ID: {$task->booking_id}\n";
        echo "  Booking Status: {$task->booking->status}\n";
        echo "  Task Status: {$task->status}\n";
        echo "  Room: {$task->booking->room->name}\n";
        echo "  Guest: {$task->booking->user->name}\n";
        echo "  ❌ DELETING...\n";
        
        $task->delete();
        echo "  ✅ Deleted!\n";
        echo "  ----------------------------------------\n";
    }
    
    echo "\n✅ Cleanup complete! Deleted {$orphanTasks->count()} orphan task(s).\n";
}
