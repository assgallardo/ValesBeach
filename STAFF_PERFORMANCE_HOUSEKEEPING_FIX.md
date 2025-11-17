# Staff Performance Report - Housekeeping Tasks Fix

## Issue Identified
The staff performance report was showing **0 housekeeping tasks** despite staff members completing multiple housekeeping tasks visible in their dashboard.

## Root Cause Analysis

### Database Architecture Discovery
The system uses **TWO separate tables** for housekeeping management:

1. **`housekeeping_requests` table**
   - Tracks the initial housekeeping trigger when a booking is checked out
   - Fields: `assigned_to`, `status`, `assigned_at`, `triggered_at`, `completed_at`
   - **Problem**: All records had `assigned_to = NULL` and `status = 'pending'`
   - This table represents the checkout trigger, NOT staff task assignments

2. **`tasks` table** (THE CORRECT SOURCE)
   - Unified task management for BOTH service requests AND housekeeping
   - Fields: `assigned_to`, `assigned_by`, `task_type`, `status`, `created_at`, `completed_at`, `service_request_id`, `booking_id`
   - `task_type` = 'housekeeping' for housekeeping tasks
   - `task_type` = 'service' for service request tasks
   - **Contains actual staff assignments and task completions**

### Data Verification Results

**Tasks Table (Correct Source):**
```
Total Housekeeping Tasks: 11
- Staff User (ID: 6): 7 assigned, 6 completed
- Staff User 2 (ID: 13): 2 assigned, 2 completed
- Unassigned: 2 tasks
```

**Task Examples:**
```
Task ID: 48 - Assigned To: 6, Status: completed, Created: 2025-11-13, Completed: 2025-11-16
Task ID: 51 - Assigned To: 6, Status: completed, Created: 2025-11-16, Completed: 2025-11-16
Task ID: 59 - Assigned To: 13, Status: completed, Created: 2025-11-16, Completed: 2025-11-16
```

## Solution Implemented

### Files Modified

**1. `app/Http/Controllers/Manager/ReportsController.php`**

**Changed From:** Querying `housekeeping_requests` table with complex date field fallbacks
**Changed To:** Querying `tasks` table with `task_type = 'housekeeping'`

#### staffPerformance() Method Updates:

**Housekeeping Assigned Tasks:**
```php
// OLD - Wrong table
$housekeepingAssigned = \App\Models\HousekeepingRequest::where('assigned_to', $staff->id)
    ->where(function($query) use ($startDate, $endDate) {
        $query->whereBetween('assigned_at', [$startDate, $endDate])
              ->orWhereBetween('triggered_at', [$startDate, $endDate])
              ->orWhereBetween('created_at', [$startDate, $endDate]);
    })
    ->count();

// NEW - Correct table
$housekeepingAssigned = Task::where('assigned_to', $staff->id)
    ->where('task_type', 'housekeeping')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->count();
```

**Housekeeping Completed Tasks:**
```php
// OLD - Wrong table
$housekeepingCompleted = \App\Models\HousekeepingRequest::where('assigned_to', $staff->id)
    ->where('status', 'completed')
    ->where(function($query) use ($startDate, $endDate) {
        $query->whereBetween('completed_at', [$startDate, $endDate])
              ->orWhereBetween('assigned_at', [$startDate, $endDate])
              ->orWhereBetween('triggered_at', [$startDate, $endDate])
              ->orWhereBetween('created_at', [$startDate, $endDate]);
    })
    ->count();

// NEW - Correct table
$housekeepingCompleted = Task::where('assigned_to', $staff->id)
    ->where('task_type', 'housekeeping')
    ->where('status', 'completed')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->count();
```

**Housekeeping Pending Tasks:**
```php
// OLD - Wrong table
$housekeepingPending = \App\Models\HousekeepingRequest::where('assigned_to', $staff->id)
    ->whereIn('status', ['assigned', 'in_progress'])
    ->where(function($query) use ($startDate, $endDate) {
        $query->whereBetween('assigned_at', [$startDate, $endDate])
              ->orWhereBetween('triggered_at', [$startDate, $endDate])
              ->orWhereBetween('created_at', [$startDate, $endDate]);
    })
    ->count();

// NEW - Correct table
$housekeepingPending = Task::where('assigned_to', $staff->id)
    ->where('task_type', 'housekeeping')
    ->whereIn('status', ['pending', 'assigned', 'in_progress'])
    ->whereBetween('created_at', [$startDate, $endDate])
    ->count();
```

**Average Completion Time:**
```php
// OLD - Wrong table with COALESCE fallback
$housekeepingAvgTime = \App\Models\HousekeepingRequest::where('assigned_to', $staff->id)
    ->whereNotNull('completed_at')
    ->where(function($query) use ($startDate, $endDate) {
        $query->whereBetween('completed_at', [$startDate, $endDate])
              ->orWhere(function($q) use ($startDate, $endDate) {
                  $q->whereNotNull('assigned_at')
                    ->whereBetween('assigned_at', [$startDate, $endDate]);
              })
              ->orWhereBetween('triggered_at', [$startDate, $endDate])
              ->orWhereBetween('created_at', [$startDate, $endDate]);
    })
    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, COALESCE(assigned_at, triggered_at, created_at), completed_at)) as avg_hours')
    ->first()->avg_hours ?? 0;

// NEW - Correct table
$housekeepingAvgTime = Task::where('assigned_to', $staff->id)
    ->where('task_type', 'housekeeping')
    ->whereNotNull('completed_at')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_hours')
    ->first()->avg_hours ?? 0;
```

#### exportStaffPerformance() Method Updates:

Same changes applied to the CSV export function to ensure consistency between the web view and exported data.

## Test Results

**Test Date Range:** 2025-10-18 to 2025-11-17

### Staff User (ID: 6)
- **Service Requests:** 1 assigned, 1 completed
- **Housekeeping Tasks:** 7 assigned, 6 completed, 1 pending
- **Combined Total:** 8 assigned, 7 completed
- **Completion Rate:** 87.5%
- **Avg Housekeeping Time:** 11.2 hours

### Staff User 2 (ID: 13)
- **Service Requests:** 1 assigned, 1 completed
- **Housekeeping Tasks:** 3 assigned, 3 completed
- **Combined Total:** 4 assigned, 4 completed
- **Completion Rate:** 100%
- **Avg Housekeeping Time:** 0.3 hours

## Technical Notes

### Task Type Field Values
- `'housekeeping'` - Housekeeping tasks created when booking checked out
- `'service'` - Service request tasks created from guest requests

### Task Status Values
- `'pending'` - Not started
- `'assigned'` - Assigned to staff
- `'in_progress'` - Staff working on it
- `'completed'` - Task finished
- `'cancelled'` - Task cancelled

### Relationships
- **Tasks ↔ Bookings:** `booking_id` field links housekeeping tasks to bookings
- **Tasks ↔ Service Requests:** `service_request_id` field links service tasks to requests
- **Tasks ↔ Users:** `assigned_to` for staff member, `assigned_by` for manager

### Controller Used by Staff
Staff view and update tasks through **`StaffTaskController`**, which reads from the `tasks` table:
- `index()` - Display assigned tasks (filters housekeeping tasks for checked_out bookings)
- `updateStatus()` - Mark task as in_progress/completed
- `updateNotes()` - Add notes to task
- `cancel()` - Cancel a task

## Impact

✅ **Staff performance reports now accurately display:**
- Housekeeping tasks assigned to each staff member
- Housekeeping tasks completed by each staff member
- Combined totals (service requests + housekeeping)
- Accurate completion rates including housekeeping work
- Average completion time for housekeeping tasks
- Proper CSV export with housekeeping breakdown

✅ **Resolved the disconnect between:**
- What staff see in their dashboard (completed housekeeping tasks)
- What managers see in performance reports (now matches staff reality)

## Verification

Run these diagnostic scripts to verify data:
1. `verify_tasks_table.php` - Check task assignments and completions
2. `test_report_logic.php` - Test report calculation logic
3. Visit Manager → Reports → Staff Performance to see live results

## Recommendations

1. **Consider deprecating `housekeeping_requests` table** if it's only used for triggering task creation
2. **Unified task system** is cleaner - all tasks (service + housekeeping) in one table
3. **Add indexes** on `tasks.task_type` and `tasks.assigned_to` for faster queries if not already present
4. **Clean up unassigned tasks** (Task IDs 43 and 62 have NULL assigned_to)

## Date: November 17, 2025
## Status: ✅ RESOLVED
