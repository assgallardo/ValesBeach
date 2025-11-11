<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CREATE MISSING HOUSEKEEPING TASKS ===\n\n";

// Find all checked-out bookings without housekeeping tasks
$checkedOutBookings = \App\Models\Booking::where('status', 'checked_out')
    ->with(['room', 'user'])
    ->get();

echo "Found {$checkedOutBookings->count()} checked-out bookings\n\n";

$created = 0;
$skipped = 0;

foreach ($checkedOutBookings as $booking) {
    // Check if task already exists
    $existingTask = \App\Models\Task::where('booking_id', $booking->id)
        ->where('task_type', 'housekeeping')
        ->first();
    
    if ($existingTask) {
        echo "Booking #{$booking->id}: Task already exists (Task #{$existingTask->id}) - SKIPPED\n";
        $skipped++;
        continue;
    }
    
    // Create the task
    try {
        $task = \App\Models\Task::create([
            'title' => 'Housekeeping Required',
            'description' => "Room cleanup required after guest check-out.\n\nFacility: {$booking->room->name}\nCategory: {$booking->room->category}\nGuest: {$booking->user->name}\nCheck-out: " . $booking->check_out->format('M d, Y g:i A'),
            'booking_id' => $booking->id,
            'task_type' => 'housekeeping',
            'assigned_by' => 4, // Admin user ID
            'assigned_to' => null,
            'status' => 'pending',
            'due_date' => now()->addHours(2),
        ]);
        
        echo "Booking #{$booking->id}: Task created successfully (Task #{$task->id})\n";
        echo "  - Facility: {$booking->room->name}\n";
        echo "  - Guest: {$booking->user->name}\n";
        echo "  - Due: {$task->due_date->format('M d, Y g:i A')}\n\n";
        
        $created++;
    } catch (\Exception $e) {
        echo "Booking #{$booking->id}: ERROR - {$e->getMessage()}\n\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Tasks created: $created\n";
echo "Tasks skipped: $skipped\n";
echo "Total processed: " . ($created + $skipped) . "\n";
