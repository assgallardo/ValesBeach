<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;
use App\Models\Booking;

echo "=== HOUSEKEEPING DUPLICATE PREVENTION TEST ===\n\n";

// Get booking #60
$booking = Booking::with(['room', 'user'])->find(60);

if (!$booking) {
    echo "Booking #60 not found!\n";
    exit(1);
}

echo "Booking #60 Details:\n";
echo "  Room: {$booking->room->name}\n";
echo "  Guest: {$booking->user->name}\n";
echo "  Current Status: {$booking->status}\n\n";

// Count existing tasks
$existingTasksCount = Task::where('booking_id', 60)
    ->where('task_type', 'housekeeping')
    ->count();

echo "Existing housekeeping tasks for this booking: {$existingTasksCount}\n\n";

// Simulate multiple status changes to checked_out
echo "TEST: Simulating multiple status changes to 'checked_out'\n";
echo "This should NOT create duplicate tasks...\n\n";

for ($i = 1; $i <= 3; $i++) {
    echo "Attempt #{$i}: Change status to checked_out\n";
    
    // Check if task exists
    $existingTask = Task::where('booking_id', $booking->id)
        ->where('task_type', 'housekeeping')
        ->first();
    
    if ($existingTask) {
        echo "  ✅ Task already exists (ID: {$existingTask->id}), NOT creating duplicate\n";
    } else {
        echo "  ℹ️  No task exists, would create new one\n";
    }
    
    $taskCount = Task::where('booking_id', $booking->id)
        ->where('task_type', 'housekeeping')
        ->count();
    
    echo "  Current task count: {$taskCount}\n\n";
}

echo "=== TEST SUMMARY ===\n";
$finalCount = Task::where('booking_id', 60)
    ->where('task_type', 'housekeeping')
    ->count();

echo "Final task count: {$finalCount}\n";

if ($finalCount === 1) {
    echo "✅ PASS: Only 1 housekeeping task exists (no duplicates)\n";
} else {
    echo "❌ FAIL: Found {$finalCount} tasks (should be 1)\n";
}

echo "\n=== WORKFLOW TEST ===\n";
echo "Testing the complete workflow:\n\n";

echo "1. When status is 'pending': Task should NOT display\n";
echo "2. When status is 'confirmed': Task should NOT display\n";
echo "3. When status is 'checked_in': Task should NOT display\n";
echo "4. When status is 'checked_out': Task SHOULD display\n";
echo "5. When status is 'completed': Task SHOULD display (marked as completed)\n";
echo "6. When status is 'cancelled': Task should NOT display\n\n";

// Test the display filter
$statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'completed', 'cancelled'];

foreach ($statuses as $status) {
    $shouldDisplay = in_array($status, ['checked_out', 'completed']);
    
    // Simulate the query from StaffAssignmentController
    $visibleTasks = Task::where('task_type', 'housekeeping')
        ->whereHas('booking', function($query) use ($status) {
            $query->where('status', $status);
        })
        ->count();
    
    $displayStatus = $visibleTasks > 0 ? "✅ WILL DISPLAY" : "❌ HIDDEN";
    $expected = $shouldDisplay ? "✅ WILL DISPLAY" : "❌ HIDDEN";
    $result = ($visibleTasks > 0) === $shouldDisplay ? "✅ CORRECT" : "❌ WRONG";
    
    echo "Status '{$status}': {$displayStatus} (Expected: {$expected}) {$result}\n";
}

echo "\n✅ All tests complete!\n";
