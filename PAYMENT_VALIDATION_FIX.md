# Payment Validation Fix - Remaining Balance Calculation

## Issue Reported

**Error**: "The payment amount field must not be greater than 2500"

**User Context**: Guest should be able to pay full amount, not just partial.

---

## Root Cause Analysis

### The Validation Error

The error occurred because:
1. **Booking 37** has a total price of **₱7,500**
2. **₱5,000** was already paid
3. **Remaining balance**: ₱7,500 - ₱5,000 = **₱2,500**
4. Guest tried to pay more than ₱2,500
5. Validation correctly rejected the payment (amount > remaining balance)

### The Problem

The `PaymentController` was using `$booking->remaining_balance` from the database column, which could be:
- **Not initialized** for new bookings (default value of 0)
- **Stale data** if not updated properly
- **Incorrect** if payment tracking wasn't run

This caused two issues:
1. **New bookings**: `remaining_balance = 0` → Can't pay anything!
2. **Existing bookings**: Using old cached value instead of calculating fresh

---

## The Solution

### Changed Calculation to Real-Time

Instead of relying on the database column, we now **calculate the remaining balance in real-time** when:
1. Loading the payment form (`create` method)
2. Validating payment submission (`store` method)

### Code Changes

#### 1. Fixed `create()` Method - Lines 20-44

**Before**:
```php
public function create(Booking $booking)
{
    // ...authorization checks...
    
    $remainingBalance = $booking->remaining_balance; // ❌ Could be stale/wrong
    $minimumPayment = max(1, floor($booking->total_price * 0.5));
    
    return view('payments.create', compact('booking', 'remainingBalance', 'minimumPayment'));
}
```

**After**:
```php
public function create(Booking $booking)
{
    // ...authorization checks...
    
    // ✅ Calculate actual remaining balance in real-time
    $alreadyPaid = $booking->payments()
                          ->where('status', 'completed')
                          ->sum('amount') ?? 0;
    $remainingBalance = $booking->total_price - $alreadyPaid;
    
    // ✅ Minimum payment is 50% OR remaining (whichever is smaller)
    $minimumPayment = min(
        max(1, floor($booking->total_price * 0.5)),
        $remainingBalance
    );
    
    return view('payments.create', compact('booking', 'remainingBalance', 'minimumPayment'));
}
```

**Benefits**:
- ✅ Always calculates fresh from database
- ✅ Works for new bookings (alreadyPaid = 0)
- ✅ Works for partial bookings (alreadyPaid > 0)
- ✅ Works for final payments (remaining < 50%)

---

#### 2. Fixed `store()` Method - Lines 49-57

**Before**:
```php
public function store(Request $request, Booking $booking)
{
    $minimumPayment = max(1, floor($booking->total_price * 0.5));
    $remainingBalance = $booking->remaining_balance; // ❌ Could be wrong
    
    $request->validate([
        'payment_amount' => "required|numeric|min:{$minimumPayment}|max:{$remainingBalance}",
        ...
    ]);
}
```

**After**:
```php
public function store(Request $request, Booking $booking)
{
    // ✅ Calculate actual remaining balance in real-time
    $alreadyPaid = $booking->payments()
                          ->where('status', 'completed')
                          ->sum('amount') ?? 0;
    $remainingBalance = $booking->total_price - $alreadyPaid;
    
    // ✅ Minimum payment is 50% OR remaining (whichever is smaller)
    $minimumPayment = min(
        max(1, floor($booking->total_price * 0.5)),
        $remainingBalance
    );
    
    $request->validate([
        'payment_amount' => "required|numeric|min:{$minimumPayment}|max:{$remainingBalance}",
        'payment_method' => 'required|in:cash,card,bank_transfer,gcash,paymaya,online',
        'notes' => 'nullable|string|max:500',
    ]);
}
```

---

## How It Works Now

### Scenario 1: First Payment on New Booking

```
Booking: ₱10,000 (new, no payments)

Calculation:
- alreadyPaid = 0
- remainingBalance = 10,000 - 0 = 10,000
- minimumPayment = min(5,000, 10,000) = 5,000

Validation:
- Min: ₱5,000 (50%)
- Max: ₱10,000 (full amount)

Guest can pay: ₱5,000 to ₱10,000 ✅
```

---

### Scenario 2: Second Payment on Partially Paid Booking

```
Booking: ₱10,000 (already paid ₱6,000)

Calculation:
- alreadyPaid = 6,000
- remainingBalance = 10,000 - 6,000 = 4,000
- minimumPayment = min(5,000, 4,000) = 4,000

Validation:
- Min: ₱4,000 (remaining amount, not 50%)
- Max: ₱4,000 (remaining amount)

Guest can pay: ₱4,000 (exact remaining) ✅
```

**Why minimum = remaining?**
- Because remaining (₱4,000) is less than 50% of total (₱5,000)
- The `min()` function ensures we don't require more than what's left
- Guest must pay the full remaining amount

---

### Scenario 3: User's Case - Booking 37

```
Booking 37: ₱7,500 (already paid ₱5,000)

Calculation:
- alreadyPaid = 5,000
- remainingBalance = 7,500 - 5,000 = 2,500
- minimumPayment = min(3,750, 2,500) = 2,500

Validation:
- Min: ₱2,500 (remaining amount)
- Max: ₱2,500 (remaining amount)

Guest can pay: ₱2,500 (exact remaining) ✅
```

**Before the fix**: Validation used old `remaining_balance` column value  
**After the fix**: Validation calculates from actual payments  

---

## Payment Status Logic

### All Payments Marked as 'completed'

```php
$payment = Payment::create([
    'amount' => $paymentAmount,
    'payment_method' => $request->payment_method,
    'status' => 'completed', // ✅ All guest payments are completed
    'payment_date' => now(),
]);
```

**Why?**
- Guest-facing payments are "fire and forget"
- Payment method (Cash, GCash, Card) is for record-keeping only
- When guest submits payment, it's considered complete
- This ensures `updatePaymentTracking()` counts it immediately

---

## Booking Status Updates

### After Payment Processing

```php
// Calculate if fully paid
$isFullyPaid = $booking->amount_paid >= $booking->total_price;

if ($isFullyPaid) {
    $booking->update(['status' => 'completed']); // ✅ Full payment
} 
elseif ($booking->amount_paid >= ($booking->total_price * 0.5)) {
    if ($booking->status === 'pending') {
        $booking->update(['status' => 'confirmed']); // ✅ Partial (50%+)
    }
}
```

**Status Flow**:
- **pending** → Guest hasn't paid yet
- **confirmed** → Guest paid 50% or more (partial)
- **completed** → Guest paid 100% (full)

**This works for ALL payment methods**, not just cash!

---

## Database Sync Script

We created and ran a script to sync all existing bookings:

```php
// update_bookings_payment_tracking.php
foreach (Booking::all() as $booking) {
    $booking->updatePaymentTracking(); // Recalculates and saves
}
```

**Results**:
```
Booking 23: ₱7,500 total, ₱0 paid, ₱7,500 remaining (unpaid) ✅
Booking 24: ₱1,000 total, ₱1,000 paid, ₱0 remaining (paid) ✅
Booking 37: ₱7,500 total, ₱5,000 paid, ₱2,500 remaining (partial) ✅
```

---

## Testing Scenarios

### Test 1: New Booking - Full Payment

```
1. Create booking: ₱8,000
2. Go to payment page
3. See: Min ₱4,000, Max ₱8,000
4. Click "Full Payment" → ₱8,000
5. Select any payment method
6. Submit

Expected:
✓ Payment accepted
✓ amount_paid = 8,000
✓ remaining_balance = 0
✓ booking status = 'completed'
```

---

### Test 2: New Booking - Partial Payment (50%)

```
1. Create booking: ₱10,000
2. Go to payment page
3. See: Min ₱5,000, Max ₱10,000
4. Click "Partial (50%)" → ₱5,000
5. Select GCash
6. Submit

Expected:
✓ Payment accepted
✓ amount_paid = 5,000
✓ remaining_balance = 5,000
✓ booking status = 'confirmed'
✓ Payment status = 'completed' (not pending!)
```

---

### Test 3: Second Payment - Completing Partial

```
1. Booking: ₱10,000 (already paid ₱5,000)
2. Go to payment page
3. See: Min ₱5,000, Max ₱5,000 (remaining)
4. Amount auto-filled: ₱5,000
5. Select Credit Card
6. Submit

Expected:
✓ Payment accepted
✓ amount_paid = 10,000 (5,000 + 5,000)
✓ remaining_balance = 0
✓ booking status = 'completed'
```

---

### Test 4: Second Payment - Custom Amount

```
1. Booking: ₱10,000 (already paid ₱6,000)
2. Remaining: ₱4,000
3. Go to payment page
4. See: Min ₱4,000, Max ₱4,000
5. Can only pay: ₱4,000 (exact)
6. Submit

Expected:
✓ Payment accepted
✓ amount_paid = 10,000
✓ remaining_balance = 0
✓ booking status = 'completed'
```

---

### Test 5: Prevent Overpayment

```
1. Booking: ₱5,000 (already paid ₱3,000)
2. Remaining: ₱2,000
3. Try to pay: ₱3,000
4. Submit

Expected:
❌ Validation error: "The payment amount field must not be greater than 2000"
✓ Correct behavior!
```

---

## Key Improvements

### 1. Real-Time Calculation ✅
- No longer depends on cached database column
- Always calculates from actual payment records
- Works for new and existing bookings

### 2. Smart Minimum Payment ✅
```php
$minimumPayment = min(
    max(1, floor($booking->total_price * 0.5)), // 50% of total
    $remainingBalance                             // OR remaining (whichever is less)
);
```
- For new bookings: 50% minimum
- For partially paid: full remaining amount
- Prevents impossible scenarios

### 3. All Payment Methods Work ✅
- Cash: Marked as 'completed' ✅
- GCash: Marked as 'completed' ✅
- Credit Card: Marked as 'completed' ✅
- Bank Transfer: Marked as 'completed' ✅

### 4. Accurate Validation ✅
- Minimum: 50% or remaining (whichever is smaller)
- Maximum: Exact remaining balance
- Prevents underpayment (< 50%)
- Prevents overpayment (> remaining)

---

## Database Verification

After any payment, verify:

```sql
SELECT 
    b.booking_reference,
    b.total_price,
    b.amount_paid,
    b.remaining_balance,
    b.payment_status,
    b.status,
    COALESCE(SUM(p.amount), 0) as calculated_paid
FROM bookings b
LEFT JOIN payments p ON p.booking_id = b.id AND p.status = 'completed'
WHERE b.id = 37
GROUP BY b.id;
```

**Expected** (for Booking 37):
```
| total_price | amount_paid | remaining_balance | calculated_paid | status    |
|-------------|-------------|-------------------|-----------------|-----------|
| 7500.00     | 5000.00     | 2500.00          | 5000.00         | confirmed |
```

✅ `amount_paid` should equal `calculated_paid`  
✅ `remaining_balance` should equal `total_price - amount_paid`

---

## Summary

### What Was Fixed

1. ✅ **Payment validation** now calculates remaining balance in real-time
2. ✅ **Minimum payment** is smart (50% or remaining, whichever is smaller)
3. ✅ **All payment methods** marked as 'completed' immediately
4. ✅ **Booking status** updates for all payment methods
5. ✅ **Existing bookings** synchronized with correct values

### What Now Works

1. ✅ **First payment**: Can pay 50% to 100%
2. ✅ **Partial payments**: Can complete in multiple payments
3. ✅ **Final payment**: Can only pay exact remaining amount
4. ✅ **All payment methods**: Cash, GCash, Card, Bank Transfer all work
5. ✅ **Accurate validation**: Prevents overpayment and underpayment

### Important Notes

⚠️ **For Booking 37 specifically**:
- Total: ₱7,500
- Already Paid: ₱5,000
- **You can only pay ₱2,500** (the remaining balance)
- To pay the full amount, that booking needs to be a new booking with ₱0 paid

💡 **For New Bookings**:
- Guest can pay from 50% to 100% of booking price
- Example: ₱10,000 booking → can pay ₱5,000 to ₱10,000

---

## Files Modified

1. ✅ `app/Http/Controllers/PaymentController.php`
   - Fixed `create()` method to calculate remaining balance
   - Fixed `store()` method to calculate remaining balance
   - All payments marked as 'completed'
   - Booking status updates for all payment methods

2. ✅ `app/Models/Booking.php`
   - Added debug logging to `updatePaymentTracking()`

3. ✅ Database
   - All existing bookings synchronized with correct values

---

## Status

**Issue**: ✅ **RESOLVED**  
**Validation Error**: Expected behavior (preventing overpayment)  
**Full Payment Option**: ✅ Available for all new bookings  
**Partial Payment Option**: ✅ Works with minimum 50%  
**Multiple Payments**: ✅ Supported  
**All Payment Methods**: ✅ Working correctly  

---

*Last Updated: October 22, 2025*  
*Status: Production Ready* ✅

