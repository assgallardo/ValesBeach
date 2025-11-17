<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Task;
use App\Models\User;

echo "=== VERIFYING TASKS TABLE ===\n\n";

// Check total tasks
$totalTasks = Task::count();
echo "Total Tasks in Database: {$totalTasks}\n\n";

// Check housekeeping tasks
$housekeepingTasks = Task::where('task_type', 'housekeeping')->get();
echo "Housekeeping Tasks: " . $housekeepingTasks->count() . "\n";

if ($housekeepingTasks->count() > 0) {
    echo "\n=== HOUSEKEEPING TASKS DETAILS ===\n";
    foreach ($housekeepingTasks as $task) {
        echo "Task ID: {$task->id}\n";
        echo "  Title: {$task->title}\n";
        echo "  Assigned To: {$task->assigned_to}\n";
        echo "  Status: {$task->status}\n";
        echo "  Created At: {$task->created_at}\n";
        echo "  Completed At: " . ($task->completed_at ?? 'NULL') . "\n";
        echo "  Booking ID: " . ($task->booking_id ?? 'NULL') . "\n";
        echo "---\n";
    }
}

// Check staff with assigned tasks
echo "\n=== STAFF TASK ASSIGNMENTS ===\n";
$staffUsers = User::where('role', 'staff')->get();
foreach ($staffUsers as $staff) {
    $assignedTasks = Task::where('assigned_to', $staff->id)->count();
    $housekeepingAssigned = Task::where('assigned_to', $staff->id)
        ->where('task_type', 'housekeeping')
        ->count();
    $housekeepingCompleted = Task::where('assigned_to', $staff->id)
        ->where('task_type', 'housekeeping')
        ->where('status', 'completed')
        ->count();
    
    echo "Staff: {$staff->name} (ID: {$staff->id})\n";
    echo "  Total Tasks Assigned: {$assignedTasks}\n";
    echo "  Housekeeping Tasks Assigned: {$housekeepingAssigned}\n";
    echo "  Housekeeping Tasks Completed: {$housekeepingCompleted}\n";
    echo "---\n";
}

// Check other task types
echo "\n=== TASK TYPES BREAKDOWN ===\n";
$taskTypes = Task::select('task_type')
    ->selectRaw('COUNT(*) as count')
    ->groupBy('task_type')
    ->get();

foreach ($taskTypes as $type) {
    echo "{$type->task_type}: {$type->count}\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
