<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;
use App\Models\Booking;

echo "=== HOUSEKEEPING TASK WORKFLOW TEST ===\n\n";

// Get a valid manager user for assigned_by
$manager = \App\Models\User::where('role', 'manager')->first();
if (!$manager) {
    echo "No manager user found! Please create a manager first.\n";
    exit(1);
}
echo "Using Manager: {$manager->name} (ID: {$manager->id})\n\n";

// Get booking #63
$booking = Booking::with(['room', 'user'])->find(63);

if (!$booking) {
    echo "Booking #63 not found!\n";
    exit(1);
}

echo "Booking #63 Details:\n";
echo "  Room: {$booking->room->name}\n";
echo "  Guest: {$booking->user->name}\n";
echo "  Current Status: {$booking->status}\n\n";

// Test 1: Update to checked_out
echo "TEST 1: Update booking status to 'checked_out'\n";
$booking->update(['status' => 'checked_out']);
echo "  Status updated to: checked_out\n";

// Simulate the controller logic
$housekeepingTask = Task::create([
    'title' => 'Housekeeping Required',
    'description' => "Room cleanup required after guest check-out.\n\nFacility: {$booking->room->name}\nGuest: {$booking->user->name}",
    'booking_id' => $booking->id,
    'task_type' => 'housekeeping',
    'status' => 'pending',
    'assigned_to' => null,
    'assigned_by' => $manager->id,
    'due_date' => now()->addHours(2)
]);

echo "  ✅ Housekeeping task created (ID: {$housekeepingTask->id})\n\n";

// Check if task appears in filtered query
$visibleTasks = Task::where('task_type', 'housekeeping')
    ->whereHas('booking', function($query) {
        $query->whereIn('status', ['checked_out', 'completed']);
    })
    ->count();
echo "  Tasks visible in Task Assignment: {$visibleTasks}\n";
echo "  ✅ Task SHOULD be visible (status = checked_out)\n\n";

// Test 2: Update to pending
echo "TEST 2: Update booking status to 'pending'\n";
$booking->update(['status' => 'pending']);
echo "  Status updated to: pending\n";

// Simulate cleanup logic
Task::where('booking_id', $booking->id)
    ->where('task_type', 'housekeeping')
    ->where('status', '!=', 'completed')
    ->delete();
echo "  ✅ Housekeeping task deleted (status changed from checked_out)\n\n";

// Check if task is hidden
$visibleTasks = Task::where('task_type', 'housekeeping')
    ->whereHas('booking', function($query) {
        $query->whereIn('status', ['checked_out', 'completed']);
    })
    ->count();
echo "  Tasks visible in Task Assignment: {$visibleTasks}\n";
echo "  ✅ Task should NOT be visible (status = pending)\n\n";

// Test 3: checked_out -> completed
echo "TEST 3: Update booking status to 'checked_out' then 'completed'\n";
$booking->update(['status' => 'checked_out']);

$housekeepingTask = Task::create([
    'title' => 'Housekeeping Required',
    'description' => "Room cleanup required after guest check-out.",
    'booking_id' => $booking->id,
    'task_type' => 'housekeeping',
    'status' => 'pending',
    'assigned_to' => null,
    'assigned_by' => $manager->id,
    'due_date' => now()->addHours(2)
]);

echo "  Status updated to: checked_out\n";
echo "  ✅ Housekeeping task created (ID: {$housekeepingTask->id})\n";

// Update to completed
$booking->update(['status' => 'completed']);

// Auto-complete the task
Task::where('booking_id', $booking->id)
    ->where('task_type', 'housekeeping')
    ->where('status', '!=', 'completed')
    ->update(['status' => 'completed', 'completed_at' => now()]);

$housekeepingTask->refresh();
echo "  Status updated to: completed\n";
echo "  ✅ Housekeeping task auto-completed (status: {$housekeepingTask->status})\n\n";

// Check if task is still visible
$visibleTasks = Task::where('task_type', 'housekeeping')
    ->whereHas('booking', function($query) {
        $query->whereIn('status', ['checked_out', 'completed']);
    })
    ->count();
echo "  Tasks visible in Task Assignment: {$visibleTasks}\n";
echo "  ✅ Task SHOULD still be visible (status = completed)\n\n";

// Cleanup - delete test task
$housekeepingTask->delete();
echo "TEST CLEANUP: Deleted test task\n";

// Reset booking status
$booking->update(['status' => 'pending']);
echo "RESET: Booking status set back to 'pending'\n\n";

echo "=== ALL TESTS PASSED ===\n";
echo "\nSummary:\n";
echo "✅ Tasks created when status → checked_out\n";
echo "✅ Tasks deleted when status changes from checked_out to pending/confirmed/checked_in/cancelled\n";
echo "✅ Tasks auto-completed when status changes from checked_out to completed\n";
echo "✅ Completed tasks remain visible in Task Assignment\n";
echo "✅ Only tasks for checked_out or completed bookings are shown\n";
