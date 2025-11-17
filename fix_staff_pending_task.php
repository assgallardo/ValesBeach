<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Task;
use App\Models\Booking;

echo "=== REMOVING IN-PROGRESS TASK FOR STAFF USER ===\n\n";

// Get the in-progress task
$task = Task::find(44);

if (!$task) {
    echo "Task not found!\n";
    exit;
}

echo "Found Task:\n";
echo "  ID: {$task->id}\n";
echo "  Title: {$task->title}\n";
echo "  Type: {$task->task_type}\n";
echo "  Status: {$task->status}\n";
echo "  Assigned To: {$task->assigned_to}\n";
echo "  Booking ID: " . ($task->booking_id ?? 'NULL') . "\n";

// Check the booking status
if ($task->booking_id) {
    $booking = Booking::find($task->booking_id);
    if ($booking) {
        echo "  Booking Status: {$booking->status}\n";
    }
}

echo "\n";

// Since the staff dashboard shows no active tasks, this task should be completed or cancelled
// Let's mark it as completed
$task->update([
    'status' => 'completed',
    'completed_at' => now()
]);

echo "✓ Task marked as completed\n";
echo "  New Status: {$task->status}\n";
echo "  Completed At: {$task->completed_at}\n";

echo "\n=== DONE ===\n";
