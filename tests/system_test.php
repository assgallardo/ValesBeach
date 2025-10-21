<?php

/**
 * Comprehensive System Test Script
 * Tests: Database connections, Models, Relationships, Methods
 * 
 * Run with: php artisan tinker < tests/system_test.php
 * Or manually in tinker
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║     VALESBEACH SYSTEM COMPREHENSIVE TEST SUITE         ║\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "\n";

// Test 1: Database Connection
echo "🔍 TEST 1: DATABASE CONNECTION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    DB::connection()->getPdo();
    echo "✅ Database connected successfully\n";
    echo "   Database: " . DB::connection()->getDatabaseName() . "\n";
    echo "   Driver: " . DB::connection()->getDriverName() . "\n";
} catch (\Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// Test 2: Table Existence
echo "🔍 TEST 2: TABLE EXISTENCE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$tables = [
    'users',
    'rooms',
    'bookings',
    'payments',
    'cottages',
    'cottage_bookings',
    'cottage_images',
    'room_maintenance_logs',
    'room_cleaning_schedules',
];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "✅ Table '$table' exists\n";
    } else {
        echo "❌ Table '$table' NOT FOUND\n";
    }
}
echo "\n";

// Test 3: Model Loading
echo "🔍 TEST 3: MODEL LOADING\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$models = [
    'App\\Models\\User',
    'App\\Models\\Room',
    'App\\Models\\Booking',
    'App\\Models\\Payment',
    'App\\Models\\Cottage',
    'App\\Models\\CottageBooking',
    'App\\Models\\CottageImage',
    'App\\Models\\RoomMaintenanceLog',
    'App\\Models\\RoomCleaningSchedule',
];

foreach ($models as $model) {
    if (class_exists($model)) {
        echo "✅ Model '$model' loaded\n";
    } else {
        echo "❌ Model '$model' NOT FOUND\n";
    }
}
echo "\n";

// Test 4: Cottage Model Methods
echo "🔍 TEST 4: COTTAGE MODEL METHODS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    // Test model instantiation
    $cottage = new App\Models\Cottage();
    echo "✅ Cottage model instantiated\n";
    
    // Test fillable attributes
    $fillable = $cottage->getFillable();
    echo "✅ Fillable attributes: " . count($fillable) . " fields\n";
    
    // Test casts
    $casts = $cottage->getCasts();
    echo "✅ Casted attributes: " . count($casts) . " fields\n";
    
    // Test methods exist
    $methods = [
        'bookings',
        'cottageImages',
        'scopeActive',
        'scopeAvailable',
        'scopeFeatured',
        'isAvailableFor',
        'calculatePrice',
    ];
    
    foreach ($methods as $method) {
        if (method_exists($cottage, $method)) {
            echo "✅ Method '$method' exists\n";
        } else {
            echo "❌ Method '$method' NOT FOUND\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ Cottage model test failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Cottage Booking Model Methods
echo "🔍 TEST 5: COTTAGE BOOKING MODEL METHODS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $booking = new App\Models\CottageBooking();
    echo "✅ CottageBooking model instantiated\n";
    
    $methods = [
        'cottage',
        'user',
        'payments',
        'updatePaymentTracking',
        'cancel',
        'confirm',
        'checkIn',
        'checkOut',
        'canBeCancelled',
        'scopeActive',
        'scopeUpcoming',
        'scopeCurrent',
    ];
    
    foreach ($methods as $method) {
        if (method_exists($booking, $method)) {
            echo "✅ Method '$method' exists\n";
        } else {
            echo "❌ Method '$method' NOT FOUND\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ CottageBooking model test failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 6: Maintenance Model Methods
echo "🔍 TEST 6: MAINTENANCE MODEL METHODS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $maintenance = new App\Models\RoomMaintenanceLog();
    echo "✅ RoomMaintenanceLog model instantiated\n";
    
    $methods = [
        'room',
        'reportedBy',
        'assignedTo',
        'scopePending',
        'scopeInProgress',
        'scopeCompleted',
        'scopeUrgent',
        'scopeHighPriority',
        'scopeOverdue',
        'markAsStarted',
        'markAsCompleted',
    ];
    
    foreach ($methods as $method) {
        if (method_exists($maintenance, $method)) {
            echo "✅ Method '$method' exists\n";
        } else {
            echo "❌ Method '$method' NOT FOUND\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ RoomMaintenanceLog model test failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 7: Cleaning Schedule Model Methods
echo "🔍 TEST 7: CLEANING SCHEDULE MODEL METHODS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $cleaning = new App\Models\RoomCleaningSchedule();
    echo "✅ RoomCleaningSchedule model instantiated\n";
    
    $methods = [
        'room',
        'booking',
        'assignedTo',
        'completedBy',
        'scopeScheduled',
        'scopeInProgress',
        'scopeCompleted',
        'scopeToday',
        'scopeOverdue',
        'scopeHighPriority',
        'markAsStarted',
        'markAsCompleted',
        'isChecklistComplete',
    ];
    
    foreach ($methods as $method) {
        if (method_exists($cleaning, $method)) {
            echo "✅ Method '$method' exists\n";
        } else {
            echo "❌ Method '$method' NOT FOUND\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ RoomCleaningSchedule model test failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 8: Database Column Verification
echo "🔍 TEST 8: DATABASE COLUMNS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Cottages table
echo "📋 Cottages table columns:\n";
$cottageColumns = Schema::getColumnListing('cottages');
$expectedCottageColumns = ['id', 'name', 'code', 'capacity', 'price_per_day', 'status', 'is_active'];
foreach ($expectedCottageColumns as $col) {
    if (in_array($col, $cottageColumns)) {
        echo "  ✅ Column '$col'\n";
    } else {
        echo "  ❌ Column '$col' MISSING\n";
    }
}
echo "  Total columns: " . count($cottageColumns) . "\n\n";

// Cottage Bookings table
echo "📋 Cottage Bookings table columns:\n";
$bookingColumns = Schema::getColumnListing('cottage_bookings');
$expectedBookingColumns = ['id', 'booking_reference', 'cottage_id', 'user_id', 'booking_type', 'payment_status', 'status'];
foreach ($expectedBookingColumns as $col) {
    if (in_array($col, $bookingColumns)) {
        echo "  ✅ Column '$col'\n";
    } else {
        echo "  ❌ Column '$col' MISSING\n";
    }
}
echo "  Total columns: " . count($bookingColumns) . "\n\n";

// Maintenance table
echo "📋 Room Maintenance Logs table columns:\n";
$maintenanceColumns = Schema::getColumnListing('room_maintenance_logs');
$expectedMaintenanceColumns = ['id', 'room_id', 'type', 'priority', 'status', 'assigned_to'];
foreach ($expectedMaintenanceColumns as $col) {
    if (in_array($col, $maintenanceColumns)) {
        echo "  ✅ Column '$col'\n";
    } else {
        echo "  ❌ Column '$col' MISSING\n";
    }
}
echo "  Total columns: " . count($maintenanceColumns) . "\n\n";

// Cleaning Schedule table
echo "📋 Room Cleaning Schedules table columns:\n";
$cleaningColumns = Schema::getColumnListing('room_cleaning_schedules');
$expectedCleaningColumns = ['id', 'room_id', 'booking_id', 'type', 'status', 'bed_made', 'bathroom_cleaned'];
foreach ($expectedCleaningColumns as $col) {
    if (in_array($col, $cleaningColumns)) {
        echo "  ✅ Column '$col'\n";
    } else {
        echo "  ❌ Column '$col' MISSING\n";
    }
}
echo "  Total columns: " . count($cleaningColumns) . "\n\n";

// Test 9: Payment Integration
echo "🔍 TEST 9: PAYMENT INTEGRATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$paymentColumns = Schema::getColumnListing('payments');
if (in_array('cottage_booking_id', $paymentColumns)) {
    echo "✅ Payment table has 'cottage_booking_id' column\n";
} else {
    echo "❌ Payment table MISSING 'cottage_booking_id' column\n";
}
echo "\n";

// Test 10: Record Counts
echo "🔍 TEST 10: DATABASE RECORD COUNTS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    echo "📊 Users: " . App\Models\User::count() . " records\n";
    echo "📊 Rooms: " . App\Models\Room::count() . " records\n";
    echo "📊 Bookings: " . App\Models\Booking::count() . " records\n";
    echo "📊 Payments: " . App\Models\Payment::count() . " records\n";
    echo "📊 Cottages: " . App\Models\Cottage::count() . " records\n";
    echo "📊 Cottage Bookings: " . App\Models\CottageBooking::count() . " records\n";
    echo "📊 Cottage Images: " . App\Models\CottageImage::count() . " records\n";
    echo "📊 Maintenance Logs: " . App\Models\RoomMaintenanceLog::count() . " records\n";
    echo "📊 Cleaning Schedules: " . App\Models\RoomCleaningSchedule::count() . " records\n";
} catch (\Exception $e) {
    echo "❌ Record count failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Summary
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║                  TEST SUITE COMPLETE                   ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";
