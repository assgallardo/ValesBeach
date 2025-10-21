# ValesBeach Resort - New Modules Implementation

## Overview
Two major modules have been added to the ValesBeach Resort Management System:
1. **Room Cleaning & Maintenance Module** - Track and manage room cleaning schedules and maintenance tasks
2. **Cottage Management Module** - Manage cottage rentals with booking system

---

## 1. ROOM CLEANING & MAINTENANCE MODULE

### Database Tables Created

#### room_maintenance_logs
Tracks all maintenance tasks, repairs, and inspections for rooms.

**Fields:**
- `id` - Primary key
- `room_id` - Foreign key to rooms table
- `reported_by` - User who reported the issue
- `assigned_to` - Staff assigned to fix
- `type` - cleaning, repair, inspection, preventive, emergency, upgrade
- `priority` - low, medium, high, urgent
- `status` - pending, in_progress, completed, cancelled, on_hold
- `title` - Short description
- `description` - Detailed description
- `notes` - Additional notes
- `resolution_notes` - How it was resolved
- `estimated_cost` - Estimated repair cost
- `actual_cost` - Final cost
- `scheduled_date` - When scheduled
- `started_at` - When work started
- `completed_at` - When completed
- `due_date` - Deadline
- `images` - JSON array of photos
- `checklist` - JSON array of tasks

#### room_cleaning_schedules
Manages room cleaning schedules and tracking.

**Fields:**
- `id` - Primary key
- `room_id` - Foreign key to rooms
- `booking_id` - Related booking (if applicable)
- `assigned_to` - Staff assigned to clean
- `completed_by` - Staff who completed
- `type` - checkout_cleaning, daily_service, deep_cleaning, turndown_service, maintenance_cleaning
- `priority` - low, normal, high, urgent
- `status` - scheduled, in_progress, completed, cancelled, skipped
- `scheduled_date` - When to clean
- `started_at` - Start time
- `completed_at` - Completion time
- `notes` - General notes
- `special_instructions` - Special requirements

**Checklist Fields:**
- `bed_made` - Boolean
- `bathroom_cleaned` - Boolean
- `floor_vacuumed` - Boolean
- `trash_removed` - Boolean
- `towels_replaced` - Boolean
- `amenities_restocked` - Boolean
- `surfaces_dusted` - Boolean
- `linens_changed` - Boolean

**Additional:**
- `custom_checklist` - JSON for extra items
- `supplies_used` - JSON tracking supplies
- `images` - Before/after photos
- `duration_minutes` - Time taken
- `quality_rating` - Rating 1-5

### Models Created

#### RoomMaintenanceLog.php
**Key Methods:**
- `room()` - Get associated room
- `reportedBy()` - Get user who reported
- `assignedTo()` - Get assigned staff
- `scopePending()` - Get pending tasks
- `scopeInProgress()` - Get active tasks
- `scopeCompleted()` - Get completed tasks
- `scopeUrgent()` - Get urgent tasks
- `scopeHighPriority()` - Get high priority
- `scopeOverdue()` - Get overdue tasks
- `markAsStarted()` - Start maintenance
- `markAsCompleted()` - Complete maintenance
- `getIsOverdueAttribute()` - Check if overdue
- `getPriorityColorAttribute()` - UI color coding
- `getStatusColorAttribute()` - UI color coding

#### RoomCleaningSchedule.php
**Key Methods:**
- `room()` - Get room
- `booking()` - Get related booking
- `assignedTo()` - Get assigned staff
- `completedBy()` - Get completer
- `scopeScheduled()` - Get scheduled cleanings
- `scopeToday()` - Get today's cleanings
- `scopeOverdue()` - Get overdue cleanings
- `markAsStarted()` - Start cleaning
- `markAsCompleted()` - Complete cleaning
- `isChecklistComplete()` - Check if all tasks done
- `getChecklistCompletionAttribute()` - Get % complete
- `getStatusColorAttribute()` - UI color coding

### Use Cases
1. **Housekeeping Dashboard** - View daily cleaning schedules
2. **Maintenance Tracking** - Log repairs and issues
3. **Quality Control** - Track cleaning quality ratings
4. **Resource Management** - Track supplies used
5. **Performance Metrics** - Duration and completion rates

---

## 2. COTTAGE MANAGEMENT MODULE

### Database Tables Created

#### cottages
Main cottage information and configuration.

**Fields:**
- `id` - Primary key
- `name` - Cottage name
- `code` - Unique identifier (COT-001)
- `description` - Full description
- `capacity` - Maximum guests
- `bedrooms` - Number of bedrooms
- `bathrooms` - Number of bathrooms

**Pricing:**
- `price_per_day` - Daily rate
- `price_per_hour` - Hourly rate
- `weekend_rate` - Weekend pricing
- `holiday_rate` - Holiday pricing
- `security_deposit` - Deposit amount
- `min_hours` - Minimum rental hours (default 4)
- `max_hours` - Maximum rental hours (default 12)

**Features:**
- `amenities` - JSON array (wifi, kitchen, grill, etc.)
- `features` - JSON array (sea_view, private_pool, etc.)
- `location` - Physical location
- `size_sqm` - Size in square meters

**Status:**
- `status` - available, occupied, maintenance, reserved, unavailable
- `is_active` - Active/inactive
- `is_featured` - Featured cottage flag

**Booking Rules:**
- `allow_day_use` - Allow daytime rental
- `allow_overnight` - Allow overnight stays
- `allow_pets` - Pets allowed
- `allow_events` - Events allowed
- `advance_booking_days` - How far ahead to book

**Images:**
- `primary_image` - Main photo
- `images` - JSON array of additional photos

**Maintenance:**
- `last_maintenance` - Last maintenance date
- `next_maintenance` - Next scheduled maintenance

**Display:**
- `sort_order` - Display order
- `is_featured` - Featured flag

#### cottage_bookings
Cottage rental bookings with flexible options.

**Fields:**
- `id` - Primary key
- `booking_reference` - Unique reference (COT-xxxxx)
- `cottage_id` - Foreign key to cottages
- `user_id` - Guest user
- `booking_type` - day_use, overnight, hourly, event

**Dates & Time:**
- `check_in_date` - Check-in date
- `check_out_date` - Check-out date
- `check_in_time` - Check-in time
- `check_out_time` - Check-out time
- `hours` - For hourly bookings
- `nights` - Number of nights

**Guest Info:**
- `guests` - Number of guests
- `children` - Number of children
- `special_requests` - Special needs

**Pricing:**
- `base_price` - Base cottage price
- `additional_guest_fee` - Extra guest charges
- `extra_hours_fee` - Overtime charges
- `weekend_surcharge` - Weekend extra
- `holiday_surcharge` - Holiday extra
- `security_deposit` - Deposit amount
- `total_price` - Final total

**Payment Tracking:**
- `amount_paid` - Total paid so far
- `remaining_balance` - Amount still owed
- `payment_status` - unpaid, partial, paid

**Status:**
- `status` - pending, confirmed, checked_in, checked_out, cancelled, completed, no_show
- `cancellation_reason` - Why cancelled
- `cancelled_at` - Cancellation timestamp
- `confirmed_at` - Confirmation timestamp
- `checked_in_at` - Check-in timestamp
- `checked_out_at` - Check-out timestamp

**Additional:**
- `addons` - JSON array of extra services
- `admin_notes` - Internal notes
- `guest_notes` - Guest notes

#### cottage_images
Cottage photo gallery.

**Fields:**
- `id` - Primary key
- `cottage_id` - Foreign key
- `image_path` - File path
- `caption` - Photo description
- `sort_order` - Display order
- `is_primary` - Primary photo flag

### Models Created

#### Cottage.php
**Key Methods:**
- `bookings()` - Get all bookings
- `cottageImages()` - Get images
- `scopeActive()` - Get active cottages
- `scopeAvailable()` - Get available cottages
- `scopeFeatured()` - Get featured cottages
- `isAvailableFor($checkIn, $checkOut)` - Check availability
- `calculatePrice($checkIn, $checkOut, $type, $hours)` - Calculate booking price
- `getFormattedPricePerDayAttribute()` - Formatted daily price
- `getStatusColorAttribute()` - UI color coding
- `getCapacityDescriptionAttribute()` - Capacity summary
- `getPrimaryImageUrlAttribute()` - Get main image

#### CottageBooking.php
**Key Methods:**
- `cottage()` - Get cottage
- `user()` - Get guest
- `payments()` - Get payment history
- `scopeActive()` - Active bookings
- `scopeUpcoming()` - Future bookings
- `scopeCurrent()` - Current stays
- `updatePaymentTracking()` - Update payment status
- `getMinimumPaymentAttribute()` - 50% minimum payment
- `canBeCancelled()` - Check if cancellable
- `cancel($reason)` - Cancel booking
- `confirm()` - Confirm booking
- `checkIn()` - Check in guest
- `checkOut()` - Check out guest
- `getFormattedTotalPriceAttribute()` - Formatted prices
- `getPaymentStatusColorAttribute()` - UI color coding
- `getStatusColorAttribute()` - UI color coding

#### CottageImage.php
**Key Methods:**
- `cottage()` - Get cottage
- `getImageUrlAttribute()` - Get full URL

### Payment Integration
- Added `cottage_booking_id` column to payments table
- Cottages support same partial payment system as rooms (50% minimum)
- Payment tracking methods integrated

---

## Features Summary

### Room Cleaning & Maintenance
✅ Track maintenance tasks by type and priority
✅ Assign staff to cleaning and maintenance
✅ Cleaning checklists with 8 standard tasks
✅ Custom checklist items support
✅ Photo upload for issues and completion
✅ Cost tracking (estimated vs actual)
✅ Duration tracking
✅ Quality ratings
✅ Overdue task detection
✅ Supply usage tracking

### Cottage Management
✅ Flexible booking types (day use, overnight, hourly, events)
✅ Capacity management (guests, bedrooms, bathrooms)
✅ Dynamic pricing (daily, hourly, weekend, holiday rates)
✅ Amenities and features tracking
✅ Multiple images per cottage
✅ Availability checking
✅ Security deposits
✅ Partial payment support (50% minimum)
✅ Booking status tracking
✅ Add-ons support
✅ Maintenance scheduling
✅ Pet and event policies

---

## Next Steps (To Be Implemented)

### Controllers Needed:
1. `RoomMaintenanceController.php` - Manage maintenance tasks
2. `CleaningScheduleController.php` - Manage cleaning schedules
3. `CottageController.php` - Admin/manager cottage management
4. `GuestCottageController.php` - Guest cottage browsing/booking
5. `CottageBookingController.php` - Handle cottage bookings

### Views Needed:

**Maintenance Module:**
- `resources/views/maintenance/index.blade.php` - List all maintenance tasks
- `resources/views/maintenance/create.blade.php` - Report new issue
- `resources/views/maintenance/show.blade.php` - View task details
- `resources/views/maintenance/edit.blade.php` - Update task
- `resources/views/cleaning/schedule.blade.php` - Daily schedule view
- `resources/views/cleaning/calendar.blade.php` - Calendar view
- `resources/views/cleaning/checklist.blade.php` - Cleaning checklist form

**Cottage Module:**
- `resources/views/guest/cottages/index.blade.php` - Browse cottages
- `resources/views/guest/cottages/show.blade.php` - Cottage details
- `resources/views/guest/cottages/book.blade.php` - Booking form
- `resources/views/admin/cottages/index.blade.php` - Manage cottages
- `resources/views/admin/cottages/create.blade.php` - Add new cottage
- `resources/views/admin/cottages/edit.blade.php` - Edit cottage
- `resources/views/admin/cottage-bookings/index.blade.php` - All bookings
- `resources/views/admin/cottage-bookings/show.blade.php` - Booking details

### Routes Needed:

```php
// Maintenance & Cleaning Routes (Staff/Admin only)
Route::middleware(['auth', 'role:admin,manager,staff'])->group(function () {
    Route::resource('maintenance', RoomMaintenanceController::class);
    Route::resource('cleaning', CleaningScheduleController::class);
    Route::post('maintenance/{log}/start', [RoomMaintenanceController::class, 'start']);
    Route::post('maintenance/{log}/complete', [RoomMaintenanceController::class, 'complete']);
    Route::post('cleaning/{schedule}/start', [CleaningScheduleController::class, 'start']);
    Route::post('cleaning/{schedule}/complete', [CleaningScheduleController::class, 'complete']);
});

// Cottage Routes (Admin/Manager)
Route::prefix('admin')->middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::resource('cottages', CottageController::class);
    Route::post('cottages/{cottage}/toggle-status', [CottageController::class, 'toggleStatus']);
    Route::get('cottage-bookings', [CottageController::class, 'bookings'])->name('admin.cottage-bookings.index');
});

// Guest Cottage Routes
Route::middleware(['auth'])->prefix('guest')->name('guest.')->group(function () {
    Route::get('cottages', [GuestCottageController::class, 'index'])->name('cottages.index');
    Route::get('cottages/{cottage}', [GuestCottageController::class, 'show'])->name('cottages.show');
    Route::post('cottages/{cottage}/check-availability', [GuestCottageController::class, 'checkAvailability']);
    Route::post('cottages/{cottage}/book', [CottageBookingController::class, 'store'])->name('cottages.book');
    Route::get('cottage-bookings', [CottageBookingController::class, 'index'])->name('cottage-bookings.index');
    Route::get('cottage-bookings/{booking}', [CottageBookingController::class, 'show'])->name('cottage-bookings.show');
});
```

---

## Database Migrations Created

1. `2025_10_22_100001_create_room_maintenance_logs_table.php` ✅
2. `2025_10_22_100002_create_room_cleaning_schedules_table.php` ✅
3. `2025_10_22_100003_create_cottages_table.php` ✅
4. `2025_10_22_100004_create_cottage_bookings_table.php` ✅
5. `2025_10_22_100005_create_cottage_images_table.php` ✅
6. `2025_10_22_100006_add_cottage_booking_id_to_payments_table.php` ✅

**All migrations have been run successfully!**

---

## Models Created

1. `app/Models/RoomMaintenanceLog.php` ✅
2. `app/Models/RoomCleaningSchedule.php` ✅
3. `app/Models/Cottage.php` ✅
4. `app/Models/CottageBooking.php` ✅
5. `app/Models/CottageImage.php` ✅

---

## Integration Points

### With Existing System:
- **Rooms** - Maintenance and cleaning linked to rooms table
- **Bookings** - Cleaning schedules triggered by checkouts
- **Users** - Staff assignments for maintenance and cleaning
- **Payments** - Cottage bookings use same payment system as rooms
- **Dashboard** - Can add maintenance/cleaning widgets

### Payment System:
- Cottage bookings support partial payments (50% minimum)
- Uses existing Payment model with new `cottage_booking_id` field
- Same payment tracking methods as room bookings
- Payment confirmation views work for cottage bookings

---

## Status: DATABASE & MODELS COMPLETE ✅

**Completed:**
- ✅ All database migrations created and run
- ✅ All models created with relationships
- ✅ Payment system integration
- ✅ Partial payment support for cottages
- ✅ Status tracking and color coding
- ✅ Scopes and helper methods

**Remaining:**
- ⏳ Controllers
- ⏳ Views
- ⏳ Routes
- ⏳ Testing

---

## File Locations

### Migrations:
`database/migrations/2025_10_22_1000xx_*.php`

### Models:
- `app/Models/RoomMaintenanceLog.php`
- `app/Models/RoomCleaningSchedule.php`
- `app/Models/Cottage.php`
- `app/Models/CottageBooking.php`
- `app/Models/CottageImage.php`

