# Quick Reference Guide - New Modules

## Room Cleaning & Maintenance

### Creating a Maintenance Log
```php
use App\Models\RoomMaintenanceLog;

$log = RoomMaintenanceLog::create([
    'room_id' => 1,
    'reported_by' => auth()->id(),
    'type' => 'repair',
    'priority' => 'high',
    'title' => 'Broken AC Unit',
    'description' => 'AC not cooling properly',
    'scheduled_date' => now()->addDay(),
    'estimated_cost' => 5000.00,
]);
```

### Creating a Cleaning Schedule
```php
use App\Models\RoomCleaningSchedule;

$schedule = RoomCleaningSchedule::create([
    'room_id' => 1,
    'booking_id' => 123, // Optional
    'type' => 'checkout_cleaning',
    'scheduled_date' => now(),
    'assigned_to' => $staffId,
]);
```

### Marking Cleaning as Complete
```php
$schedule->update([
    'bed_made' => true,
    'bathroom_cleaned' => true,
    'floor_vacuumed' => true,
    // ... all checklist items
]);

$schedule->markAsCompleted(30, 4.5); // 30 minutes, 4.5 rating
```

### Queries
```php
// Get today's cleaning schedule
$cleanings = RoomCleaningSchedule::today()->with('room', 'assignedTo')->get();

// Get pending maintenance
$maintenance = RoomMaintenanceLog::pending()->highPriority()->get();

// Get overdue tasks
$overdue = RoomMaintenanceLog::overdue()->get();
```

---

## Cottage Management

### Creating a Cottage
```php
use App\Models\Cottage;

$cottage = Cottage::create([
    'name' => 'Sunset Villa',
    'code' => 'COT-001',
    'description' => 'Beautiful beachfront cottage',
    'capacity' => 6,
    'bedrooms' => 2,
    'bathrooms' => 2,
    'price_per_day' => 5000.00,
    'price_per_hour' => 500.00,
    'weekend_rate' => 6000.00,
    'amenities' => ['wifi', 'kitchen', 'grill', 'tv'],
    'features' => ['sea_view', 'beachfront'],
    'location' => 'Beachfront',
    'status' => 'available',
    'allow_day_use' => true,
    'allow_overnight' => true,
]);
```

### Checking Availability
```php
$cottage = Cottage::find(1);
$available = $cottage->isAvailableFor('2025-10-25', '2025-10-27');
```

### Calculating Price
```php
$price = $cottage->calculatePrice(
    '2025-10-25',
    '2025-10-27',
    'overnight'
);
```

### Creating a Cottage Booking
```php
use App\Models\CottageBooking;

$booking = CottageBooking::create([
    'cottage_id' => 1,
    'user_id' => auth()->id(),
    'booking_type' => 'overnight',
    'check_in_date' => '2025-10-25',
    'check_out_date' => '2025-10-27',
    'guests' => 4,
    'children' => 2,
    'base_price' => 5000.00,
    'total_price' => 10000.00,
    'remaining_balance' => 10000.00,
]);
```

### Managing Booking Status
```php
// Confirm booking
$booking->confirm();

// Check in
$booking->checkIn();

// Check out
$booking->checkOut();

// Cancel
$booking->cancel('Guest requested cancellation');
```

### Payment Processing
```php
// After payment is made
$booking->updatePaymentTracking();

// Check minimum payment
$minPayment = $booking->minimum_payment; // 50% of remaining
```

### Queries
```php
// Get available cottages
$available = Cottage::active()->available()->get();

// Get featured cottages
$featured = Cottage::featured()->get();

// Get upcoming bookings
$upcoming = CottageBooking::upcoming()->with('cottage', 'user')->get();

// Get current occupancy
$current = CottageBooking::current()->get();
```

---

## Color Coding Reference

### Maintenance Priority
- **Urgent**: Red (`text-red-600 bg-red-100`)
- **High**: Orange (`text-orange-600 bg-orange-100`)
- **Medium**: Yellow (`text-yellow-600 bg-yellow-100`)
- **Low**: Blue (`text-blue-600 bg-blue-100`)

### Maintenance Status
- **Completed**: Green (`text-green-600 bg-green-100`)
- **In Progress**: Blue (`text-blue-600 bg-blue-100`)
- **Pending**: Yellow (`text-yellow-600 bg-yellow-100`)
- **On Hold**: Gray (`text-gray-600 bg-gray-100`)
- **Cancelled**: Red (`text-red-600 bg-red-100`)

### Cottage Status
- **Available**: Green (`text-green-600 bg-green-100`)
- **Occupied**: Blue (`text-blue-600 bg-blue-100`)
- **Reserved**: Yellow (`text-yellow-600 bg-yellow-100`)
- **Maintenance**: Orange (`text-orange-600 bg-orange-100`)
- **Unavailable**: Red (`text-red-600 bg-red-100`)

### Payment Status
- **Paid**: Green (`text-green-600 bg-green-100`)
- **Partial**: Yellow (`text-yellow-600 bg-yellow-100`)
- **Unpaid**: Red (`text-red-600 bg-red-100`)

---

## Enum Values

### Maintenance Type
- `cleaning`
- `repair`
- `inspection`
- `preventive`
- `emergency`
- `upgrade`

### Cleaning Type
- `checkout_cleaning`
- `daily_service`
- `deep_cleaning`
- `turndown_service`
- `maintenance_cleaning`

### Booking Type (Cottage)
- `day_use`
- `overnight`
- `hourly`
- `event`

### Booking Status
- `pending`
- `confirmed`
- `checked_in`
- `checked_out`
- `cancelled`
- `completed`
- `no_show`

---

## Relationships

### Room Maintenance Log
- `belongsTo` Room
- `belongsTo` User (reportedBy)
- `belongsTo` User (assignedTo)

### Room Cleaning Schedule
- `belongsTo` Room
- `belongsTo` Booking (optional)
- `belongsTo` User (assignedTo)
- `belongsTo` User (completedBy)

### Cottage
- `hasMany` CottageBooking
- `hasMany` CottageImage

### Cottage Booking
- `belongsTo` Cottage
- `belongsTo` User
- `hasMany` Payment

### Payment (Updated)
- `belongsTo` Booking (room)
- `belongsTo` CottageBooking (cottage)
- `belongsTo` ServiceRequest
- `belongsTo` User

---

## Useful Accessors

### Maintenance
- `$log->is_overdue` - Boolean
- `$log->priority_color` - Tailwind classes
- `$log->status_color` - Tailwind classes
- `$log->formatted_estimated_cost` - ₱5,000.00
- `$log->formatted_actual_cost` - ₱4,500.00

### Cleaning
- `$schedule->is_overdue` - Boolean
- `$schedule->checklist_completion` - 0-100
- `$schedule->status_color` - Tailwind classes
- `$schedule->priority_color` - Tailwind classes

### Cottage
- `$cottage->formatted_price_per_day` - ₱5,000.00
- `$cottage->formatted_price_per_hour` - ₱500.00
- `$cottage->status_color` - Tailwind classes
- `$cottage->primary_image_url` - Full URL
- `$cottage->capacity_description` - "6 guests • 2 bedrooms • 2 bathrooms"

### Cottage Booking
- `$booking->minimum_payment` - 50% calculation
- `$booking->formatted_total_price` - ₱10,000.00
- `$booking->formatted_amount_paid` - ₱5,000.00
- `$booking->formatted_remaining_balance` - ₱5,000.00
- `$booking->payment_status_color` - Tailwind classes
- `$booking->status_color` - Tailwind classes
- `$booking->payment_status_label` - "Fully Paid" / "Partially Paid" / "Unpaid"

---

## Validation Examples

### Cottage Booking Validation
```php
$request->validate([
    'cottage_id' => 'required|exists:cottages,id',
    'check_in_date' => 'required|date|after:today',
    'check_out_date' => 'required|date|after:check_in_date',
    'guests' => 'required|integer|min:1',
    'booking_type' => 'required|in:day_use,overnight,hourly,event',
]);
```

### Maintenance Log Validation
```php
$request->validate([
    'room_id' => 'required|exists:rooms,id',
    'type' => 'required|in:cleaning,repair,inspection,preventive,emergency,upgrade',
    'priority' => 'required|in:low,medium,high,urgent',
    'title' => 'required|string|max:255',
    'description' => 'required|string',
]);
```

---

## Testing Commands

```bash
# Check tables were created
php artisan tinker
>>> Schema::hasTable('room_maintenance_logs')
>>> Schema::hasTable('room_cleaning_schedules')
>>> Schema::hasTable('cottages')
>>> Schema::hasTable('cottage_bookings')

# Test model creation
>>> $cottage = App\Models\Cottage::create(['name' => 'Test', 'code' => 'TEST-001', ...])
>>> $booking = App\Models\CottageBooking::create([...])
>>> $log = App\Models\RoomMaintenanceLog::create([...])
```

