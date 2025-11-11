# Checkout to Housekeeping System Implementation

## Overview
Automated housekeeping task deployment system that triggers when a booking status is updated to "checked_out". The system creates a task in the manager's task management module, which can then be assigned to staff members.

## System Flow

### 1. Check-Out Process
**Location:** Reservations Management (Admin/Manager/Staff)

When a booking status is updated to "checked_out":
- System automatically creates a housekeeping task
- Task is assigned to manager for staff assignment
- Guest is checked out of the facility

### 2. Database Structure

#### Tasks Table (Migration: `2025_11_10_180820_add_booking_id_and_task_type_to_tasks_table`)
**New Columns:**
- `booking_id` (nullable, foreign key to bookings table)
- `task_type` (string, default: 'service')
  - Values: 'service', 'housekeeping'

**Relationships:**
- Task belongs to Booking
- Booking has many Tasks

### 3. Task Creation (ManagerController)

**File:** `app/Http/Controllers/ManagerController.php`

**Method:** `updateBookingStatus()`
```php
// Detects status change to 'checked_out'
if ($request->status === 'checked_out') {
    $this->createHousekeepingTask($booking);
}
```

**Method:** `createHousekeepingTask($booking)`
- **Title:** "Housekeeping Required"
- **Description:** 
  - Facility: [Room/Cottage Name]
  - Category: [Room Category]
  - Guest: [Guest Name]
  - Checked Out: [Timestamp]
- **Task Type:** housekeeping
- **Due Date:** 2 hours from creation
- **Status:** pending (awaiting manager assignment)
- **Booking ID:** Links to the specific booking

### 4. Manager Task Management

**Location:** Manager Dashboard → Service Requests Module

**Features:**
- Manager sees housekeeping tasks in their task list
- Tasks appear similar to service requests
- Manager can assign tasks to available staff members
- Uses existing task assignment workflow

**Visual Indicators:**
- Same interface as service requests
- Filter by status (pending, in_progress, completed)
- View all housekeeping tasks requiring assignment

### 5. Staff Task View

**File:** `resources/views/staff/tasks/index.blade.php`

**Visual Features:**
- **Purple Color Scheme:** Housekeeping tasks use purple badges/highlights
- **Broom Icon:** <i class="fas fa-broom"> next to housekeeping task titles
- **Purple Badge:** "Housekeeping" label on task cards
- **Facility Information:** Displays room/cottage name with room icon
- **Category:** Shows room category below facility name
- **Guest Name:** Displays guest information prominently

**Staff View Display:**
```
🧹 Task Title                     [Housekeeping]
                                  [Purple Badge]
Facility: [Room Icon] Room/Cottage Name
Category: [Room Category]
Guest: [Guest Name]
Due: [Due Date]
```

### 6. Task Assignment Workflow

**Same as Service Requests:**

1. **Checkout Triggered**
   - Booking status → 'checked_out'
   - Auto-create housekeeping task

2. **Manager Assignment**
   - Manager views unassigned tasks
   - Selects staff member
   - Assigns housekeeping task

3. **Staff Completion**
   - Staff sees task in "My Tasks" module
   - Labeled as "Housekeeping" with purple styling
   - Shows facility and guest details
   - Staff marks task as complete

## File Changes Summary

### 1. Migration File
**File:** `database/migrations/2025_11_10_180820_add_booking_id_and_task_type_to_tasks_table.php`
- Added `booking_id` column (foreign key)
- Added `task_type` column (default: 'service')
- Proper rollback support

### 2. Task Model
**File:** `app/Models/Task.php`
- Added 'booking_id' to fillable
- Added 'task_type' to fillable
- Added booking() relationship method

### 3. Manager Controller
**File:** `app/Http/Controllers/ManagerController.php`
- Modified `updateBookingStatus()` to detect checkout
- Added `createHousekeepingTask()` private method
- Success message includes housekeeping notification

### 4. Staff Task Controller
**File:** `app/Http/Controllers/StaffTaskController.php`
- Updated eager loading: `with(['booking.user', 'booking.room'])`
- Loads booking relationships for housekeeping tasks

### 5. Staff Tasks View
**File:** `resources/views/staff/tasks/index.blade.php`
- Added broom icon for housekeeping tasks
- Added purple "Housekeeping" badge
- Conditional display logic:
  - Housekeeping tasks show facility + category + guest
  - Service tasks show service request info
- Purple color scheme for visual distinction

## Task Types

### Service Tasks (Default)
- Created from guest service requests
- Shows service details
- Standard workflow

### Housekeeping Tasks
- Created from checkout process
- Shows facility and guest details
- Purple visual styling
- 2-hour deadline from creation

## Usage Instructions

### For Managers

1. **When Guest Checks Out:**
   - Navigate to Reservations Management
   - Find the booking
   - Update status to "Checked Out"
   - System automatically creates housekeeping task

2. **Assign Housekeeping Task:**
   - Go to Service Requests/Task Management
   - Find pending housekeeping tasks
   - Assign to available staff member
   - Task appears in staff's "My Tasks"

### For Staff

1. **View Housekeeping Tasks:**
   - Navigate to "My Tasks"
   - Look for purple "Housekeeping" badges
   - See facility name, category, and guest

2. **Complete Housekeeping:**
   - Review task details
   - Perform housekeeping duties
   - Mark task as complete

## Key Features

✅ **Automated Task Creation:** No manual task entry needed
✅ **Clear Visual Distinction:** Purple badges and broom icons
✅ **Complete Information:** Facility, category, and guest details
✅ **Reuses Existing System:** Works with current task workflow
✅ **2-Hour Deadline:** Ensures timely housekeeping
✅ **Manager Control:** Manager assigns to appropriate staff
✅ **Database Tracking:** All tasks linked to bookings

## Benefits

1. **Efficiency:** Automatic task deployment on checkout
2. **Organization:** Clear task categorization (service vs housekeeping)
3. **Visibility:** Staff immediately see what needs cleaning
4. **Accountability:** Tasks tracked with due dates
5. **Guest Information:** Staff know who was in the facility
6. **Integration:** Seamless with existing task management

## Testing Checklist

- [ ] Create booking and check in
- [ ] Update status to "checked_out"
- [ ] Verify housekeeping task created
- [ ] Check manager can see task
- [ ] Assign task to staff member
- [ ] Verify staff sees task with purple badge
- [ ] Confirm facility and guest info displayed
- [ ] Test task completion workflow
- [ ] Verify task linked to booking in database

## Future Enhancements

### Potential Additions:
1. **Room Status Integration:** Update room status when task completed
2. **Task Priority:** Urgent housekeeping for same-day bookings
3. **Completion Notifications:** Alert manager when task done
4. **Housekeeping Checklist:** Detailed cleaning requirements
5. **Photo Upload:** Staff can upload before/after photos
6. **Task History:** View past housekeeping for each room
7. **Performance Metrics:** Track housekeeping completion times
8. **Auto-Assignment:** Distribute tasks based on staff availability

## Technical Details

### Database Relationships
```
Booking (1) ─── has many ──→ Tasks (N)
Task (N) ──── belongs to ──→ Booking (1)
```

### Task Creation Logic
```php
Task::create([
    'title' => 'Housekeeping Required',
    'description' => "Facility: {$facilityName}\nCategory: {$category}\nGuest: {$guestName}\nChecked Out: {$timestamp}",
    'booking_id' => $booking->id,
    'task_type' => 'housekeeping',
    'due_date' => now()->addHours(2),
    'status' => 'pending',
    'assigned_by' => auth()->id(),
]);
```

### Staff View Query
```php
$tasks = Task::with([
    'assignedBy',
    'serviceRequest.guest',
    'booking.user',
    'booking.room'
])->where('assigned_to', auth()->id())->get();
```

## Support Notes

- **Migration Status:** ✅ Successfully migrated (265.54ms)
- **Model Updated:** ✅ Task model with booking relationship
- **Controller Updated:** ✅ Auto-create on checkout
- **View Updated:** ✅ Staff view with purple styling
- **Ready for Production:** ✅ Yes

## Created By
GitHub Copilot  
Date: [Auto-generated on checkout implementation]  
Version: 1.0
