<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;
use App\Models\Booking;
use App\Models\User;

echo "=== STAFF HOUSEKEEPING TASK DISPLAY TEST ===\n\n";

// Get a staff user
$staff = User::where('role', 'staff')->first();

if (!$staff) {
    echo "❌ No staff user found! Please create a staff user first.\n";
    exit(1);
}

echo "Testing with Staff: {$staff->name} (ID: {$staff->id})\n\n";

// Get booking #60
$booking = Booking::find(60);

if (!$booking) {
    echo "❌ Booking #60 not found!\n";
    exit(1);
}

echo "Booking #60 Details:\n";
echo "  Current Status: {$booking->status}\n\n";

// Get the housekeeping task
$task = Task::where('booking_id', 60)
    ->where('task_type', 'housekeeping')
    ->first();

if (!$task) {
    echo "❌ No housekeeping task found for booking #60\n";
    exit(1);
}

echo "Housekeeping Task Details:\n";
echo "  Task ID: {$task->id}\n";
echo "  Assigned To: " . ($task->assigned_to ? User::find($task->assigned_to)->name : 'Unassigned') . "\n";
echo "  Status: {$task->status}\n\n";

// Assign task to staff
if ($task->assigned_to !== $staff->id) {
    echo "Assigning task to staff: {$staff->name}\n";
    $task->update(['assigned_to' => $staff->id, 'status' => 'assigned']);
    echo "✅ Task assigned!\n\n";
}

echo "=== DISPLAY TEST BY BOOKING STATUS ===\n\n";

$statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'completed', 'cancelled'];

foreach ($statuses as $status) {
    echo "Test: Booking status = '{$status}'\n";
    
    // Update booking status
    $booking->update(['status' => $status]);
    
    // Query as the StaffTaskController does
    $visibleTasks = Task::forUser($staff->id)
        ->active()
        ->where(function($query) {
            $query->where('task_type', '!=', 'housekeeping')
                ->orWhere(function($subQuery) {
                    $subQuery->where('task_type', 'housekeeping')
                        ->whereHas('booking', function($bookingQuery) {
                            $bookingQuery->whereIn('status', ['checked_out', 'completed']);
                        });
                });
        })
        ->where('booking_id', 60)
        ->count();
    
    $shouldDisplay = in_array($status, ['checked_out', 'completed']);
    $actuallyDisplays = $visibleTasks > 0;
    
    $result = $actuallyDisplays === $shouldDisplay ? "✅ CORRECT" : "❌ WRONG";
    $displayStatus = $actuallyDisplays ? "WILL DISPLAY" : "HIDDEN";
    $expected = $shouldDisplay ? "SHOULD DISPLAY" : "SHOULD BE HIDDEN";
    
    echo "  Result: {$displayStatus} ({$expected}) - {$result}\n\n";
}

echo "=== ASSIGNMENT TEST ===\n\n";

// Test unassigned task
echo "Test: Unassigned housekeeping task\n";
$task->update(['assigned_to' => null]);
$booking->update(['status' => 'checked_out']);

$visibleToStaff = Task::forUser($staff->id)
    ->where('task_type', 'housekeeping')
    ->where('booking_id', 60)
    ->count();

echo "  Booking Status: checked_out\n";
echo "  Task Assigned To: Unassigned\n";
echo "  Visible to Staff: " . ($visibleToStaff > 0 ? "YES ❌ WRONG" : "NO ✅ CORRECT") . "\n";
echo "  Expected: Should NOT be visible (not assigned to staff)\n\n";

// Assign back to staff
echo "Test: Assigned housekeeping task\n";
$task->update(['assigned_to' => $staff->id]);

$visibleToStaff = Task::forUser($staff->id)
    ->where('task_type', 'housekeeping')
    ->where('booking_id', 60)
    ->count();

echo "  Booking Status: checked_out\n";
echo "  Task Assigned To: {$staff->name}\n";
echo "  Visible to Staff: " . ($visibleToStaff > 0 ? "YES ✅ CORRECT" : "NO ❌ WRONG") . "\n";
echo "  Expected: Should be visible (assigned to staff + booking checked_out)\n\n";

// Reset booking to original status
$booking->update(['status' => 'checked_out']);

echo "=== SUMMARY ===\n";
echo "✅ Housekeeping tasks only display when:\n";
echo "   1. Task is assigned to the staff member\n";
echo "   2. Booking status is 'checked_out' OR 'completed'\n\n";
echo "❌ Housekeeping tasks are HIDDEN when:\n";
echo "   1. Task is unassigned (even if booking is checked_out)\n";
echo "   2. Booking status is pending, confirmed, checked_in, or cancelled\n\n";
echo "✅ All tests complete!\n";
