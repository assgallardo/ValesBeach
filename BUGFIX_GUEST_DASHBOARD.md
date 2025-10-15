# Bug Fix: Guest Dashboard Error

## Issue
Guest user dashboard was showing an error and couldn't be accessed.

**Error Message:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'user_id' in 'where clause'
SQL: select count(*) as aggregate from `service_requests` where `user_id` = 4 and `status` != cancelled
```

## Root Cause
The `GuestController.php` was using `user_id` to query the `service_requests` table, but the actual column name in the table is `guest_id`.

## Solution
Updated `app/Http/Controllers/GuestController.php` line 39:

**Before:**
```php
$service_requests_count = ServiceRequest::where('user_id', $user->id)
                                       ->where('status', '!=', 'cancelled')
                                       ->count();
```

**After:**
```php
$service_requests_count = ServiceRequest::where('guest_id', $user->id)
                                       ->where('status', '!=', 'cancelled')
                                       ->count();
```

## Files Modified
- `app/Http/Controllers/GuestController.php` (line 39)
- Backup created at: `app/Http/Controllers/GuestController.php.backup`

## Testing
After the fix:
1. Cleared application cache: `php artisan cache:clear`
2. Cleared configuration cache: `php artisan config:clear`
3. Cleared view cache: `php artisan view:clear`

## Guest Dashboard Features
The guest dashboard now correctly displays:
- Recent bookings (last 5)
- Booking statistics (total, pending, confirmed, completed)
- Available rooms count
- Active service requests count (excluding cancelled)

## Test Credentials
Log in with the guest account to test:
- Email: guest@valesbeach.com
- Password: guest123

## Date Fixed
October 15, 2025
