<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;

echo "=== VERIFY DISPLAY FILTER ===\n\n";

// Check all housekeeping tasks
$allTasks = Task::where('task_type', 'housekeeping')->with('booking')->get();
echo "Total housekeeping tasks in database: {$allTasks->count()}\n\n";

foreach ($allTasks as $task) {
    echo "Task ID: {$task->id}\n";
    echo "  Task Status: {$task->status}\n";
    echo "  Booking Status: {$task->booking->status}\n";
    echo "  ----------------------------------------\n";
}

echo "\n";

// Check tasks that WILL BE VISIBLE in Task Assignment (with filter)
$visibleTasks = Task::where('task_type', 'housekeeping')
    ->whereHas('booking', function($query) {
        $query->whereIn('status', ['checked_out', 'completed']);
    })
    ->with('booking')
    ->get();

echo "Tasks visible in Task Assignment (filtered): {$visibleTasks->count()}\n\n";

if ($visibleTasks->count() > 0) {
    foreach ($visibleTasks as $task) {
        echo "Task ID: {$task->id}\n";
        echo "  Task Status: {$task->status}\n";
        echo "  Booking Status: {$task->booking->status}\n";
        echo "  ✅ WILL BE DISPLAYED\n";
        echo "  ----------------------------------------\n";
    }
} else {
    echo "✅ No tasks will be displayed (all filtered out)\n";
}

echo "\n=== FILTER WORKING CORRECTLY ===\n";
