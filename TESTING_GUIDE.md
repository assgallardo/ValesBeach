# 🧪 Quick Testing Guide - All Booking Systems

## How to Test Each Booking System

### 1. 🏨 Test Room Bookings

**Steps:**
1. Login as a guest user
2. Go to Dashboard → Click "Book Rooms" (green button)
3. Browse available rooms (should NOT show cottages)
4. Click "Book Now" on any room
5. Fill in:
   - Check-in date (must be today or later)
   - Check-out date (must be after check-in)
   - Number of guests (1 to room capacity)
   - Special requests (optional)
6. Click "Book Now"
7. ✅ Should redirect to bookings page with success message

**What to Verify:**
- ✅ No Umbrella Cottages or Bahay Kubo appear in rooms list
- ✅ Price calculation shows correctly
- ✅ Date validation prevents invalid dates
- ✅ Guest count validation enforces room capacity
- ✅ Booking appears in "My Bookings"

---

### 2. 🏖️ Test Cottage Bookings

**Steps:**
1. Login as a guest user
2. Go to Dashboard → Click "Book Cottages" (amber button)
3. Browse available cottages (Umbrella Cottages & Bahay Kubo)
4. Click "Book Now" on any cottage
5. **Test Different Booking Types:**

#### A. Day Use Booking
- Select "Day Use" radio button
- ✅ Checkout date field should be HIDDEN
- Fill in:
  - Check-in date
  - Number of guests
  - Special requests
- Click "Book Now"

#### B. Overnight Booking
- Select "Overnight Booking" radio button
- ✅ Checkout date field should APPEAR
- Fill in:
  - Check-in date
  - **Checkout date** (now required)
  - Number of guests
  - Special requests
- Click "Book Now"

#### C. Hourly Booking
- Select "Hourly Booking" radio button
- ✅ Checkout date field should be HIDDEN
- Fill in fields and submit

#### D. Event Booking
- Select "Event/Function" radio button
- ✅ Checkout date field should be HIDDEN
- Fill in fields and submit

**What to Verify:**
- ✅ Umbrella Cottages show ₱350/day, ₱50/hr
- ✅ Bahay Kubo shows ₱200/day, ₱30/hr
- ✅ Checkout date appears ONLY for overnight bookings
- ✅ Form validation prevents submission without required fields
- ✅ Booking appears in "My Cottage Bookings"

---

### 3. 🛎️ Test Service Requests

**Steps:**
1. Login as a guest user
2. Go to Dashboard → Click "Request Services"
3. Browse available services
4. Click "Book Service" on any service
5. Fill in:
   - Preferred date (must be future date)
   - Preferred time (select from dropdown)
   - Number of guests
   - Special requests (optional)
6. Click "Submit Request"
7. ✅ Should redirect with success message

**What to Verify:**
- ✅ 20 services available
- ✅ Service categories display correctly (spa, dining, transportation, activities)
- ✅ Date validation prevents past dates
- ✅ Guest count validates against service capacity
- ✅ Request appears in service history

---

### 4. 🍽️ Test Food Orders

**Steps:**
1. Login as a guest user
2. Go to Dashboard → Click "Order Food"
3. Browse menu items
4. Add items to cart
5. Click "Cart" icon
6. Click "Proceed to Checkout"
7. **Test Different Delivery Types:**

#### A. Room Service
- Select "Room Service" radio button
- ✅ Delivery location field should APPEAR
- Enter room number
- ✅ Delivery fee: +₱5.00

#### B. Pickup
- Select "Pickup at Restaurant" radio button
- ✅ Delivery location field should be HIDDEN
- ✅ Delivery fee: ₱0.00

#### C. Dining Room
- Select "Serve in Dining Room" radio button
- ✅ Delivery location field should be HIDDEN
- ✅ Delivery fee: ₱0.00

8. Fill in special instructions (optional)
9. Click "Place Order"
10. ✅ Should redirect to orders page with confirmation

**What to Verify:**
- ✅ Cart count updates correctly
- ✅ Prices calculate correctly
- ✅ Delivery fee applies only for room service
- ✅ Tax (8%) calculates correctly
- ✅ Order appears in "My Orders"

---

## 🐛 Common Issues to Check

### Checkout Date Not Showing (Cottage Bookings)
**Issue:** Checkout date field doesn't appear when "Overnight" is selected  
**Cause:** Alpine.js scope issue (FIXED)  
**Verify:** The field should smoothly appear/disappear when toggling booking types

### Invalid Date Errors
**Issue:** Form rejects valid dates  
**Verify:** 
- Check-in must be today or later
- Check-out must be after check-in
- Service dates must be in the future

### Price Not Calculating
**Issue:** Total price shows ₱0.00  
**Verify:**
- Room: Nights × price per night
- Cottage: Varies by booking type
- Food: Subtotal + delivery fee + tax

### Wrong Items Showing
**Issue:** Cottages appear in rooms list or vice versa  
**Verify:**
- Rooms list: Should show 11 rooms (no cottages)
- Cottages list: Should show 17 cottages (10 Umbrella, 7 Bahay Kubo)

---

## 📊 Database Verification Queries

```sql
-- Check rooms (should NOT have cottage types)
SELECT type, COUNT(*) FROM rooms 
WHERE type IN ('Umbrella Cottage', 'Bahay Kubo') 
GROUP BY type;
-- Expected: 0 rows

-- Check cottages count
SELECT type, COUNT(*) FROM cottages GROUP BY type;
-- Expected: Umbrella Cottage (10), Bahay Kubo (7)

-- Check cottage pricing
SELECT name, price_per_day, price_per_hour FROM cottages;
-- Expected: Umbrella (350, 50), Bahay Kubo (200, 30)

-- Check recent bookings
SELECT 'rooms' as type, COUNT(*) FROM bookings 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
UNION ALL
SELECT 'cottages', COUNT(*) FROM cottage_bookings 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
UNION ALL
SELECT 'services', COUNT(*) FROM service_requests 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
UNION ALL
SELECT 'food', COUNT(*) FROM food_orders 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY);
```

---

## ✅ Success Criteria

All booking systems pass if:

1. **No console errors** in browser developer tools
2. **No validation errors** when submitting valid data
3. **Conditional fields** show/hide correctly
4. **Price calculations** are accurate
5. **Database records** are created successfully
6. **Confirmation messages** appear after booking
7. **Bookings appear** in respective history/list pages
8. **No duplicate entries** in database
9. **Alpine.js reactivity** works smoothly (cottages)
10. **All data validations** enforce business rules

---

## 🚨 If Something Breaks

### Check These Files:

**Cottage Booking Issues:**
- `resources/views/guest/cottages/book.blade.php` (line 33: x-data location)
- `app/Http/Controllers/CottageBookingController.php` (store method validation)

**Room Booking Issues:**
- `resources/views/guest/rooms/book.blade.php`
- `app/Http/Controllers/BookingController.php` (store method)

**Service Request Issues:**
- `resources/views/guest/services/request.blade.php`
- `app/Http/Controllers/GuestServiceController.php` (store method)

**Food Order Issues:**
- `resources/views/food-orders/checkout.blade.php`
- `app/Http/Controllers/FoodOrderController.php` (placeOrder method)

### Run Test Script:
```powershell
php test_all_bookings.php
```

This will check all systems and report any errors.

---

**Last Updated:** October 23, 2025  
**All Systems Status:** ✅ OPERATIONAL
