<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Task;
use App\Models\User;
use App\Models\ServiceRequest;
use Carbon\Carbon;

echo "=== TESTING STAFF PERFORMANCE REPORT LOGIC ===\n\n";

// Set date range (last 30 days to capture all recent tasks)
$endDate = Carbon::now();
$startDate = Carbon::now()->subDays(30);

echo "Date Range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}\n\n";

// Get staff users
$staffUsers = User::where('role', 'staff')->get();

foreach ($staffUsers as $staff) {
    echo "=== STAFF: {$staff->name} (ID: {$staff->id}) ===\n";
    
    // Service requests
    $serviceAssigned = ServiceRequest::where('assigned_to', $staff->id)
        ->whereBetween('assigned_at', [$startDate, $endDate])
        ->count();
    $serviceCompleted = ServiceRequest::where('assigned_to', $staff->id)
        ->where('status', 'completed')
        ->whereBetween('assigned_at', [$startDate, $endDate])
        ->count();
    $servicePending = ServiceRequest::where('assigned_to', $staff->id)
        ->whereIn('status', ['assigned', 'in_progress'])
        ->whereBetween('assigned_at', [$startDate, $endDate])
        ->count();
    
    // Housekeeping tasks from tasks table
    $housekeepingAssigned = Task::where('assigned_to', $staff->id)
        ->where('task_type', 'housekeeping')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
    $housekeepingCompleted = Task::where('assigned_to', $staff->id)
        ->where('task_type', 'housekeeping')
        ->where('status', 'completed')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
    $housekeepingPending = Task::where('assigned_to', $staff->id)
        ->where('task_type', 'housekeeping')
        ->whereIn('status', ['pending', 'assigned', 'in_progress'])
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
    
    // Combined metrics
    $totalAssigned = $serviceAssigned + $housekeepingAssigned;
    $totalCompleted = $serviceCompleted + $housekeepingCompleted;
    $totalPending = $servicePending + $housekeepingPending;
    $completionRate = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100, 1) : 0;
    
    echo "Service Requests:\n";
    echo "  Assigned: {$serviceAssigned}\n";
    echo "  Completed: {$serviceCompleted}\n";
    echo "  Pending: {$servicePending}\n";
    
    echo "\nHousekeeping Tasks:\n";
    echo "  Assigned: {$housekeepingAssigned}\n";
    echo "  Completed: {$housekeepingCompleted}\n";
    echo "  Pending: {$housekeepingPending}\n";
    
    echo "\nCombined Totals:\n";
    echo "  Total Assigned: {$totalAssigned}\n";
    echo "  Total Completed: {$totalCompleted}\n";
    echo "  Total Pending: {$totalPending}\n";
    echo "  Completion Rate: {$completionRate}%\n";
    
    // Average completion time for housekeeping
    $housekeepingAvgTime = Task::where('assigned_to', $staff->id)
        ->where('task_type', 'housekeeping')
        ->whereNotNull('completed_at')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_hours')
        ->first()->avg_hours ?? 0;
    
    echo "  Avg Housekeeping Completion Time: " . round($housekeepingAvgTime, 1) . " hours\n";
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

echo "=== TEST COMPLETE ===\n";
echo "The report logic should now correctly display housekeeping tasks!\n";
