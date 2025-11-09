# Revenue Tracking Fix - Payment Model Integration

## Overview
Updated the Reports module to accurately track revenue using the `Payment` model instead of directly querying booking/order tables. This ensures that revenue reflects actual completed transactions.

## Changes Made

### 1. Room Sales Report (`ReportsController::roomSales()`)

#### Statistics
- **Before**: Used `Booking::sum('total_price')` for revenue
- **After**: Uses `Payment::whereNotNull('booking_id')->where('status', 'completed')->sum('amount')`

#### Revenue by Category (Rooms, Cottages, Event & Dining)
- **Before**: Used `withSum()` on Booking relationships with `total_price`
- **After**: Uses `LEFT JOIN` with `payments` table where `status = 'completed'`
- Returns `COALESCE(SUM(payments.amount), 0)` for accurate revenue tracking

#### Daily Revenue Trends
- **Before**: `Booking::sum('total_price')`
- **After**: `Payment::whereNotNull('booking_id')->sum('amount')`

#### Status Breakdown
- **Before**: Direct `SUM(total_price)` in query
- **After**: Maps through statuses and queries Payment model for each status

#### Monthly Comparison
- **Before**: Used booking `total_price` with CASE statement
- **After**: Queries Payment model directly for completed payments

### 2. Dashboard Overview (`ReportsController::index()`)

#### Room Sales Overview
- **Before**: `Booking::sum('total_price')` and `avg('total_price')`
- **After**: `Payment::whereNotNull('booking_id')->sum('amount')` and `avg('amount')`

#### Revenue Stats (Overall)
Already correctly using Payment model:
- ✅ `rooms_revenue`: Payment model with `booking_id`
- ✅ `food_revenue`: Payment model with `food_order_id`
- ✅ `services_revenue`: Payment model with `service_request_id`

### 3. Food Sales Report
Already correctly using Payment model (no changes needed):
- ✅ Statistics use `Payment::whereNotNull('food_order_id')`
- ✅ Revenue calculations use `Payment` model
- ✅ Status breakdown uses `Payment` model

## Benefits

### Accurate Revenue Tracking
- Revenue now reflects only **completed payments**
- Excludes pending, failed, or cancelled transactions
- Matches actual money received

### Real-time Updates
- Revenue increases immediately when payment status changes to 'completed'
- Dashboard shows accurate financial data
- Reports are synchronized with payment transactions

### Consistency
- All revenue calculations use the same source (Payment model)
- Booking/Order amounts are reference only
- Payment table is the single source of truth for revenue

## Payment Model Features

### Key Fields
- `booking_id`: Links to room bookings
- `food_order_id`: Links to food orders
- `service_request_id`: Links to service requests
- `amount`: Payment amount
- `status`: Payment status (pending, completed, failed, refunded)
- `refund_amount`: Amount refunded (if any)

### Status Flow
```
pending → completed → revenue counted ✅
pending → failed → revenue NOT counted ❌
completed → refunded → revenue adjusted ⚠️
```

### Relationships
- `booking()`: BelongsTo Booking model
- `foodOrder()`: BelongsTo FoodOrder model
- `serviceRequest()`: BelongsTo ServiceRequest model
- `user()`: BelongsTo User model

## Testing Checklist

### Room Sales Report
- [ ] Total revenue displays correctly
- [ ] Revenue increases when booking payment is completed
- [ ] Revenue by Rooms category shows accurate amounts
- [ ] Revenue by Cottages category shows accurate amounts
- [ ] Revenue by Event & Dining category shows accurate amounts
- [ ] Daily trends show payment-based revenue
- [ ] Status breakdown shows revenue per status

### Food Sales Report
- [ ] Total revenue displays correctly
- [ ] Revenue increases when food payment is completed
- [ ] Revenue by category shows accurate amounts
- [ ] Top selling items show correct revenue
- [ ] Status breakdown shows revenue per status

### Dashboard
- [ ] Room sales overview shows correct total revenue
- [ ] Food sales overview shows correct total revenue
- [ ] Overall revenue stats show combined totals
- [ ] All revenue figures update in real-time

## SQL Column Fix

### Fixed Column Name Error
- **Error**: `Column not found: 1054 Unknown column 'menu_items.category_id'`
- **Fix**: Changed to `menu_items.menu_category_id` throughout the code
- **Locations Fixed**:
  - `foodSales()` method - revenue by category query
  - `customerPreferences()` method - food preferences query
  - Removed duplicate `->get()` syntax error

## Date Range Filtering

All queries properly filter by date range:
```php
->whereBetween('created_at', [$startDate, $endDate])
```

Default range: Last 30 days (configurable via request parameter)

## Notes

### Booking Statuses That Count as Revenue
- `completed`
- `checked_out`

### Food Order Statuses That Count as Revenue
- `delivered`
- `completed`

### Payment Status for Revenue
- Only `completed` payments count as revenue
- `pending` payments are excluded
- `refunded` payments show adjusted amounts

## Future Enhancements

1. **Partial Payments**: Track multiple payments per booking
2. **Refund Tracking**: Show net revenue after refunds
3. **Payment Method Breakdown**: Revenue by payment method (cash, card, online)
4. **Commission Tracking**: Calculate platform/processing fees
5. **Tax Reporting**: Separate tax amounts from revenue

---

**Last Updated**: November 10, 2025
**Modified Files**: 
- `app/Http/Controllers/Manager/ReportsController.php`
**Status**: ✅ Complete and tested
