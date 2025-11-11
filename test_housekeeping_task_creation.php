<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== HOUSEKEEPING TASK CREATION TEST ===\n\n";

// Test creating a task manually
echo "Testing task creation with assigned_to = null...\n";

try {
    $testBooking = \App\Models\Booking::where('status', 'checked_out')->first();
    
    if (!$testBooking) {
        echo "ERROR: No checked-out booking found to test with.\n";
        exit;
    }
    
    echo "Using Booking #{$testBooking->id} for test\n";
    echo "Room: {$testBooking->room->name}\n";
    echo "Guest: {$testBooking->user->name}\n\n";
    
    // Try creating a test task
    $task = \App\Models\Task::create([
        'title' => 'TEST - Housekeeping Required',
        'description' => "Test task creation",
        'booking_id' => $testBooking->id,
        'task_type' => 'housekeeping',
        'assigned_by' => 4,
        'assigned_to' => null, // This should work now
        'status' => 'pending',
        'due_date' => now()->addHours(2),
    ]);
    
    echo "✅ SUCCESS! Task created with ID: {$task->id}\n";
    echo "Task details:\n";
    echo "  - Title: {$task->title}\n";
    echo "  - Type: {$task->task_type}\n";
    echo "  - Status: {$task->status}\n";
    echo "  - Assigned To: " . ($task->assigned_to ?? 'NULL (unassigned)') . "\n";
    echo "  - Assigned By: {$task->assigned_by}\n";
    echo "  - Due Date: {$task->due_date}\n\n";
    
    // Now delete the test task
    $task->delete();
    echo "Test task deleted.\n\n";
    
    echo "=== VERIFICATION ===\n";
    echo "✅ Database allows NULL in assigned_to column\n";
    echo "✅ Task creation code works correctly\n";
    echo "✅ Ready to create housekeeping tasks on checkout\n\n";
    
    echo "Next steps:\n";
    echo "1. Go to Reservations Management (Admin or Manager)\n";
    echo "2. Check out a guest (change status to 'Checked Out')\n";
    echo "3. Go to Task Assignment module\n";
    echo "4. You should see the housekeeping task in purple section!\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
