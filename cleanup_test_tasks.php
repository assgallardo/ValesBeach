<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;

echo "=== CLEANUP TEST TASKS ===\n\n";

$tasks = Task::where('task_type', 'housekeeping')->get();

if ($tasks->count() === 0) {
    echo "No tasks to clean up.\n";
} else {
    foreach ($tasks as $task) {
        echo "Deleting Task ID: {$task->id}\n";
        $task->delete();
    }
    echo "\n✅ Deleted {$tasks->count()} task(s)\n";
}
