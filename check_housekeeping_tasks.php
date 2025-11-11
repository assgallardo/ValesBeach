<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;
use App\Models\Booking;

echo "=== HOUSEKEEPING TASKS CHECK ===\n\n";

$tasks = Task::where('task_type', 'housekeeping')->with('booking')->get();

if ($tasks->count() === 0) {
    echo "No housekeeping tasks found.\n";
} else {
    foreach ($tasks as $task) {
        echo "Task ID: {$task->id}\n";
        echo "  Task Status: {$task->status}\n";
        echo "  Booking ID: {$task->booking_id}\n";
        
        if ($task->booking) {
            echo "  Booking Status: {$task->booking->status}\n";
            echo "  Room: {$task->booking->room->name}\n";
            echo "  Guest: {$task->booking->user->name}\n";
        } else {
            echo "  Booking Status: DELETED/NOT FOUND\n";
        }
        
        echo "  Created: {$task->created_at}\n";
        echo "  ----------------------------------------\n";
    }
}

echo "\nTotal housekeeping tasks: " . $tasks->count() . "\n";
