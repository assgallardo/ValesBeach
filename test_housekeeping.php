<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== HOUSEKEEPING RECORDS CHECK ===\n";
echo "Total housekeeping records: " . \App\Models\HousekeepingRequest::count() . "\n";
echo "Completed records: " . \App\Models\HousekeepingRequest::where('status', 'completed')->count() . "\n\n";

$sample = \App\Models\HousekeepingRequest::first();
if ($sample) {
    echo "Sample record:\n";
    echo "  ID: {$sample->id}\n";
    echo "  Assigned to: {$sample->assigned_to}\n";
    echo "  Status: {$sample->status}\n";
    echo "  Assigned at: " . ($sample->assigned_at ?? 'NULL') . "\n";
    echo "  Triggered at: " . ($sample->triggered_at ?? 'NULL') . "\n";
    echo "  Created at: {$sample->created_at}\n";
    echo "  Completed at: " . ($sample->completed_at ?? 'NULL') . "\n";
}

// Check for specific staff users
echo "\n=== STAFF ASSIGNMENTS ===\n";
$staff = \App\Models\User::where('role', 'staff')->get();
foreach ($staff as $member) {
    $count = \App\Models\HousekeepingRequest::where('assigned_to', $member->id)->count();
    $completed = \App\Models\HousekeepingRequest::where('assigned_to', $member->id)
        ->where('status', 'completed')->count();
    echo "{$member->name} (ID: {$member->id}): {$count} total, {$completed} completed\n";
}
