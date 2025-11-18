# Service Request Scheduled Date & View Toggle Implementation

## Overview
This update implements two major improvements to the Service Request Management system:
1. **Read-only scheduled date/time** - Display guest's requested booking schedule instead of editable deadline
2. **Compact/List view toggle** - Allow managers and staff to switch between compact grid and full-width list layouts

## Changes Made

### 1. Manager View - Service Request Cards (resources/views/manager/staff-assignment/index.blade.php)

#### Scheduled Date Display (Read-Only)
- **Replaced**: Editable "Due On" deadline field with read-only "Scheduled Date & Time"
- **Data Source**: Uses `scheduled_date` from service_requests table (guest's requested schedule)
- **Display Format**: "M d, Y g:i A" (e.g., "Nov 19, 2025 3:00 PM")
- **Visual Indicator**: Blue badge showing relative time (e.g., "2 hours from now")
- **Icon**: Calendar check icon to indicate scheduled appointment

```php
<!-- Before: Editable deadline -->
<input type="datetime-local" onchange="updateDeadline(...)" />

<!-- After: Read-only scheduled_date -->
<p>{{ $request->scheduled_date->format('M d, Y g:i A') }}</p>
```

#### Removed Features
- ❌ Removed editable deadline input field
- ❌ Removed `updateDeadline()` JavaScript function
- ❌ Removed bulk deadline assignment field

#### View Mode Toggle
- **Added**: Compact/List view toggle buttons in filter bar
- **Compact View** (Default):
  - Grid layout: 2 columns on large screens
  - Smaller cards with essential information
  - Better overview of multiple requests
  
- **List View**:
  - Full-width stacked cards
  - More detailed information visible
  - Easier to read individual requests

**Toggle Controls**:
```html
<button onclick="setViewMode('compact')" id="compactViewBtn">
  <i class="fas fa-th mr-1"></i>Compact
</button>
<button onclick="setViewMode('list')" id="listViewBtn">
  <i class="fas fa-list mr-1"></i>List
</button>
```

**JavaScript Implementation**:
- Saves preference to localStorage
- Switches between grid/stack layouts
- Updates button states
- Applies to both service requests and housekeeping tasks
- Works for both active and completed views

### 2. Staff View - Tasks (resources/views/staff/tasks/index.blade.php)

#### View Toggle (Already Implemented)
- Staff view already had view toggle functionality
- Displays tasks with `due_date` field which comes from service request's `scheduled_date`
- No additional changes needed

### 3. Data Model (app/Models/ServiceRequest.php)

The model already supports:
- `scheduled_date` - Guest's requested booking date/time (datetime, casted)
- `deadline` - Optional internal deadline for staff (still exists but not shown in UI)

```php
protected $fillable = [
    'scheduled_date',  // Guest's requested schedule (READ-ONLY in UI)
    'deadline',        // Internal deadline (not shown in manager/staff views)
    // ... other fields
];

protected $casts = [
    'scheduled_date' => 'datetime',
    'deadline' => 'datetime',
];
```

## Benefits

### For Guests
✅ Their requested service date/time is clearly displayed
✅ No confusion about when service will be provided
✅ Staff can't accidentally change their booked schedule

### For Managers
✅ Clear view of guest's requested schedule
✅ Can switch between compact/list views based on preference
✅ Compact view for quick overview of many requests
✅ List view for detailed review of individual requests
✅ View preference persists across sessions

### For Staff
✅ See exact scheduled time guest requested
✅ Can switch between compact/list task views
✅ Better task organization and visibility

## Database Fields

**service_requests table**:
- `scheduled_date` (datetime) - Guest's booked service date/time ✅ DISPLAYED
- `deadline` (datetime, nullable) - Internal staff deadline ❌ HIDDEN FROM UI

## User Interface Changes

### Manager - Service Request Management

**Filter Bar**:
```
[All Status ▼] [All Staff ▼] [Clear Filters]  [Completed Tasks] [Bulk Actions] [🔲 Compact | ☰ List]
```

**Service Request Card**:
```
┌─────────────────────────────────────────────────────────┐
│ Catering Package A - Pinoy Classic    ₱5,000.00         │
│ ◉ Assigned                                              │
│                                                         │
│ GUEST                    ASSIGNED TO                    │
│ Mark Villanueva         Staff User 2                    │
│                                                         │
│ SCHEDULED DATE & TIME    DURATION & NOTES               │
│ Nov 19, 2025 3:00 PM    1 hour                         │
│ 📅 2 hours from now     Manager notes...               │
│                                                         │
│ Created: Nov 18, 2025 08:00                            │
└─────────────────────────────────────────────────────────┘
```

**View Modes**:
- **Compact**: 2-column grid, smaller cards
- **List**: 1-column stack, larger cards

### Staff - My Tasks

Already has the same view toggle functionality with compact/list modes.

## Testing Checklist

- [x] Service requests show `scheduled_date` instead of editable deadline
- [x] Scheduled date displays in correct format (M d, Y g:i A)
- [x] Relative time badge shows correctly (e.g., "2 hours from now")
- [x] View toggle switches between compact and list modes
- [x] View preference persists after page reload (localStorage)
- [x] Compact view shows 2-column grid on large screens
- [x] List view shows full-width stacked cards
- [x] Toggle works for both active and completed views
- [x] Toggle applies to both service requests and housekeeping tasks
- [x] Bulk actions no longer include deadline field
- [x] Staff tasks already use scheduled date via due_date field

## Files Modified

1. **resources/views/manager/staff-assignment/index.blade.php**
   - Replaced deadline field with scheduled_date display (read-only)
   - Removed updateDeadline() JavaScript function
   - Added view mode toggle buttons
   - Added setViewMode() JavaScript function
   - Removed bulk deadline input field
   - Updated bulkAssign() to remove deadline parameter

2. **resources/views/staff/tasks/index.blade.php**
   - Already has view toggle implemented
   - No changes needed (uses due_date which comes from scheduled_date)

## Future Considerations

- If managers need to set internal deadlines different from guest's schedule, consider adding a separate "Internal Deadline" field that doesn't affect the guest-facing scheduled date
- Consider adding a visual indicator when internal deadline differs from scheduled date
- May add filtering by scheduled date range in the future

## Related Documentation

- See `BOOKING_ACTIONS_IMPLEMENTATION.md` for service request creation flow
- See `ADMIN_PAYMENT_MANAGEMENT_GROUPED.md` for payment handling related to service requests
