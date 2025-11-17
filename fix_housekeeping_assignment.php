<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING HOUSEKEEPING TASKS ===\n\n";

$allTasks = \App\Models\HousekeepingRequest::all();
foreach ($allTasks as $task) {
    echo "Task ID {$task->id}:\n";
    echo "  Room: {$task->room_id}\n";
    echo "  Assigned To: " . ($task->assigned_to ?? 'NULL (NOT ASSIGNED!)') . "\n";
    echo "  Status: {$task->status}\n";
    echo "  Triggered: {$task->triggered_at}\n";
    echo "  Assigned At: " . ($task->assigned_at ?? 'NULL') . "\n";
    echo "  Completed At: " . ($task->completed_at ?? 'NULL') . "\n";
    echo "\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total tasks: " . $allTasks->count() . "\n";
echo "Tasks with assigned_to = NULL: " . $allTasks->where('assigned_to', null)->count() . "\n";
echo "Tasks with status = completed: " . $allTasks->where('status', 'completed')->count() . "\n";

echo "\n=== STAFF USERS ===\n";
$staff = \App\Models\User::where('role', 'staff')->get();
foreach ($staff as $member) {
    echo "{$member->name} (ID: {$member->id}, Email: {$member->email})\n";
}
