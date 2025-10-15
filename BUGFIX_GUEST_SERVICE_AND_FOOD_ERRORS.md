# Bug Fix: Guest Dashboard - Service Requests & Food Ordering Errors

## Issues Fixed

### 1. Service Request History Error ❌
**Error Message:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'user_id' in 'where clause'
SQL: select count(*) as aggregate from `service_requests` where (`guest_id` = 4 or `user_id` = 4 or `guest_email` = guest@valesbeach.com)
```

**Root Cause:** 
The `GuestServiceController.php` was querying the `service_requests` table with `user_id` column that doesn't exist. The table only has `guest_id` and `guest_email` columns.

**Solution:**
Removed all `->orWhere('user_id', $user->id)` references from the service request queries.

**File Modified:** `app/Http/Controllers/GuestServiceController.php`

**Changes:**
- **Line ~272**: Removed `->orWhere('user_id', $user->id)` from the main query
- **Line ~280**: Removed `->orWhere('user_id', $user->id)` from stats query

**Before:**
```php
$serviceRequests = ServiceRequest::with(['service'])
    ->where(function($query) use ($user) {
        $query->where('guest_id', $user->id)
              ->orWhere('user_id', $user->id)      // ❌ Column doesn't exist
              ->orWhere('guest_email', $user->email);
    })
    ->orderBy('created_at', 'desc')
    ->paginate(10);
```

**After:**
```php
$serviceRequests = ServiceRequest::with(['service'])
    ->where(function($query) use ($user) {
        $query->where('guest_id', $user->id)
              ->orWhere('guest_email', $user->email);  // ✅ Fixed
    })
    ->orderBy('created_at', 'desc')
    ->paginate(10);
```

---

### 2. Food Ordering Menu Error ❌
**Error Message:**
```
View [layouts.app] not found. (View: C:\Users\sethy\ValesBeach\resources\views\food-orders\menu.blade.php)
```

**Root Cause:** 
All food-order views were trying to extend `layouts.app` which doesn't exist. The correct layout for guests is `layouts.guest`.

**Solution:**
Changed all food-order views to extend `layouts.guest` instead of `layouts.app`.

**Files Modified:**
- `resources/views/food-orders/menu.blade.php`
- `resources/views/food-orders/cart.blade.php`
- `resources/views/food-orders/checkout.blade.php`
- `resources/views/food-orders/orders.blade.php`
- `resources/views/food-orders/show.blade.php`

**Changes:**
```blade
// Before ❌
@extends('layouts.app')

// After ✅
@extends('layouts.guest')
```

---

## Additional Fixes from Previous Session

### 3. Guest Dashboard Service Request Count Error (Fixed Earlier)
**File:** `app/Http/Controllers/GuestController.php`
**Change:** Line 39 - Changed `where('user_id', $user->id)` to `where('guest_id', $user->id)`

---

## Database Schema Reference

### service_requests table structure:
```sql
- id (bigint, primary key)
- service_id (bigint, foreign key)
- guest_id (bigint, foreign key) ✅ Use this
- guest_name (varchar)
- guest_email (varchar) ✅ Use this
- room_id (bigint, nullable, foreign key)
- service_type (varchar)
- description (text)
- scheduled_date (datetime)
- deadline (datetime)
- status (enum)
- priority (enum)
- assigned_to (bigint, nullable)
- assigned_at (datetime, nullable)
- created_at (timestamp)
- updated_at (timestamp)

Note: NO user_id column exists! ❌
```

### Available Layouts:
```
✅ resources/views/layouts/guest.blade.php   (for guest users)
✅ resources/views/layouts/admin.blade.php   (for admin users)
✅ resources/views/layouts/manager.blade.php (for managers)
✅ resources/views/layouts/staff.blade.php   (for staff)
❌ resources/views/layouts/app.blade.php     (DOES NOT EXIST)
```

---

## Testing

### Test Service Requests:
1. Log in as guest: `guest@valesbeach.com` / `guest123`
2. Navigate to Service Requests page
3. Verify page loads without errors
4. Check that service request history displays correctly
5. Verify statistics show (pending, in progress, completed, cancelled counts)

### Test Food Ordering:
1. Log in as guest: `guest@valesbeach.com` / `guest123`
2. Navigate to Food Menu
3. Verify menu page loads without "View not found" error
4. Test cart, checkout, and orders pages
5. Verify all pages use the correct guest layout

### Cache Clearing:
After fixes, run:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## Backups Created
- `app/Http/Controllers/GuestController.php.backup`
- `app/Http/Controllers/GuestServiceController.php.backup`

---

## Summary

✅ **Service Request History** - Fixed column name mismatch (removed non-existent `user_id` column)  
✅ **Food Ordering Views** - Fixed missing layout (changed from `layouts.app` to `layouts.guest`)  
✅ **Guest Dashboard** - Previously fixed service request count query  

All guest dashboard features should now work correctly:
- Dashboard overview
- Bookings management
- Service requests (create, view history, cancel)
- Food ordering (menu, cart, checkout, order history)
- Room browsing

---

## Date Fixed
October 15, 2025

## Test Account
- Email: guest@valesbeach.com
- Password: guest123
- Role: guest
