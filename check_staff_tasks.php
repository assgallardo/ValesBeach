<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Task;
use App\Models\User;

echo "=== CHECKING STAFF USER TASKS ===\n\n";

// Get Staff User (ID: 6)
$staff = User::find(6);
if (!$staff) {
    echo "Staff User not found!\n";
    exit;
}

echo "Staff: {$staff->name} (ID: {$staff->id}, Email: {$staff->email})\n\n";

// Get all tasks for this staff member
$allTasks = Task::where('assigned_to', $staff->id)->get();

echo "Total Tasks: " . $allTasks->count() . "\n\n";

if ($allTasks->count() > 0) {
    echo "=== TASK DETAILS ===\n";
    foreach ($allTasks as $task) {
        echo "Task ID: {$task->id}\n";
        echo "  Title: {$task->title}\n";
        echo "  Type: {$task->task_type}\n";
        echo "  Status: {$task->status}\n";
        echo "  Due Date: " . ($task->due_date ?? 'NULL') . "\n";
        echo "  Created: {$task->created_at}\n";
        echo "  Completed: " . ($task->completed_at ?? 'NULL') . "\n";
        
        // Check if this is a pending or active task
        $isActive = !in_array($task->status, ['completed', 'cancelled']);
        echo "  Active: " . ($isActive ? 'YES' : 'NO') . "\n";
        echo "---\n";
    }
}

// Count by status
echo "\n=== TASK COUNTS BY STATUS ===\n";
$pending = Task::where('assigned_to', $staff->id)->where('status', 'pending')->count();
$inProgress = Task::where('assigned_to', $staff->id)->where('status', 'in_progress')->count();
$completed = Task::where('assigned_to', $staff->id)->where('status', 'completed')->count();
$cancelled = Task::where('assigned_to', $staff->id)->where('status', 'cancelled')->count();

echo "Pending: {$pending}\n";
echo "In Progress: {$inProgress}\n";
echo "Completed: {$completed}\n";
echo "Cancelled: {$cancelled}\n";

echo "\n=== DONE ===\n";
