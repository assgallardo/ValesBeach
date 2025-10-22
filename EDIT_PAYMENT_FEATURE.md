# Edit Payment Feature - Complete Implementation

## Overview

Guests can now **edit their payment details** within 5 minutes of creation. This allows them to change:
- ✅ Payment amount
- ✅ Payment method  
- ✅ Payment notes

**Key Feature**: Edit the SAME payment record instead of creating a new one.

---

## What Was Implemented

### 1. **Routes Added**
Location: `routes/web.php` (Lines 149-150)

```php
Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
Route::patch('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
```

---

### 2. **Controller Methods Added**
Location: `app/Http/Controllers/PaymentController.php`

#### `edit()` Method (Lines 154-189)
```php
public function edit(Payment $payment)
{
    // Authorization check
    // Time limit check (5 minutes)
    // Calculate remaining balance (excluding this payment)
    // Load edit view with payment data
}
```

**Features**:
- ✅ Checks if user owns the payment
- ✅ Validates payment was made less than 5 minutes ago
- ✅ Prevents editing of refunded/failed payments
- ✅ Calculates available balance (excluding current payment amount)

#### `update()` Method (Lines 194-282)
```php
public function update(Request $request, Payment $payment)
{
    // Authorization check
    // Time limit check
    // Validate new payment details
    // Update payment record
    // Recalculate booking totals
    // Redirect to confirmation
}
```

**Features**:
- ✅ Updates payment amount, method, and notes
- ✅ Recalculates booking payment tracking
- ✅ Updates booking status based on new total
- ✅ Logs the update for audit trail
- ✅ Transaction-wrapped for data integrity

---

### 3. **Edit Payment View Created**
Location: `resources/views/payments/edit.blade.php`

**Sections**:
1. **Time Limit Warning** - Countdown timer showing remaining edit time
2. **Booking Summary** - Shows booking details and available balance
3. **Current Payment Info** - Displays current payment details
4. **Update Form** - Form to edit amount, method, and notes

**Features**:
- ✅ Pre-filled with current payment data
- ✅ Live countdown timer (minutes:seconds)
- ✅ Auto-disable form after 5 minutes
- ✅ Quick select buttons (minimum/maximum)
- ✅ Payment method cards (same as create page)
- ✅ Real-time validation

---

### 4. **Confirmation Page Updated**
Location: `resources/views/payments/confirmation.blade.php`

**Changes**:
- Added "Edit This Payment" button (shows only within 5 minutes)
- Renamed "Make Different Payment" to "Make Another Payment"
- Updated Important Notice to mention edit feature
- Added conditional logic based on time elapsed

---

## How It Works

### User Flow

```
1. Guest makes payment (₱2,500)
   ↓
2. Redirected to confirmation page
   ↓
3. Sees two options:
   - 🔵 "Edit This Payment" (available for 5 minutes)
   - 🟡 "Make Another Payment" (always available)
   ↓
4. Guest clicks "Edit This Payment"
   ↓
5. Taken to edit page with pre-filled form
   ↓
6. Guest changes amount to ₱3,000 and method to GCash
   ↓
7. Clicks "Update Payment"
   ↓
8. System updates the payment record (NOT creating new one)
   ↓
9. Redirected to confirmation with success message
   ↓
10. Payment history shows ONLY ONE payment (₱3,000 GCash)
```

---

## Confirmation Page Buttons

### Within 5 Minutes of Payment:

```
┌─────────────────────────────────────────────┐
│ 🔵 Edit This Payment                        │
│    Change amount or method                  │
├─────────────────────────────────────────────┤
│ 🟡 Make Another Payment                     │
│    Add new payment                          │
└─────────────────────────────────────────────┘
```

### After 5 Minutes:

```
┌─────────────────────────────────────────────┐
│ 🟡 Make Another Payment                     │
│    Add new payment                          │
└─────────────────────────────────────────────┘
```
(Edit button disappears)

---

## Edit Page Features

### 1. **Time Limit Warning**

```
┌─────────────────────────────────────────────┐
│ ⏰ Time Limit                               │
│ Payments can only be edited within         │
│ 5 minutes of creation.                      │
│ Time remaining: 4m 32s                      │ ← Live countdown
└─────────────────────────────────────────────┘
```

**What happens at 0:00**:
- Timer shows "Expired"
- All form inputs disabled
- Submit button disabled
- Guest must return to confirmation

---

### 2. **Booking Summary**

```
┌─────────────────────────────────────────────┐
│ Booking Summary                             │
│ • Booking Reference: VB38                   │
│ • Room: Executive Cottage                   │
│ • Total Amount: ₱5,000.00                   │
│ • Other Payments: ₱0.00                     │
│ • Available Balance: ₱5,000.00              │
└─────────────────────────────────────────────┘
```

**"Other Payments"**: Sum of all other payments (excluding this one)  
**"Available Balance"**: How much can be entered (total - other payments)

---

### 3. **Current Payment Info**

```
┌─────────────────────────────────────────────┐
│ ℹ️ Current Payment Details                  │
│ Amount: ₱2,500.00 |                         │
│ Method: GCash |                             │
│ Created: Oct 22, 2025 2:30 PM               │
└─────────────────────────────────────────────┘
```

Shows what the payment currently is (before editing).

---

### 4. **Update Form**

Same interface as create payment page:
- Amount input with peso symbol
- Quick select buttons (Minimum/Maximum)
- Payment method cards (Cash, Card, GCash, Bank Transfer)
- Notes field
- Update/Cancel buttons

---

## Business Logic

### Calculation Example

**Scenario**: Booking for ₱10,000

#### Step 1: First Payment
```
Guest pays: ₱4,000 (GCash)
Database:
  - Payment #1: ₱4,000 GCash
  - Booking amount_paid: ₱4,000
  - Booking remaining_balance: ₱6,000
```

#### Step 2: Guest Edits Payment (within 5 minutes)
```
Guest wants to change to ₱6,000 Cash

Edit Page Calculation:
  - Total Booking: ₱10,000
  - Other Payments: ₱0 (excluding Payment #1)
  - Available Balance: ₱10,000
  - Guest enters: ₱6,000
  - Guest selects: Cash

Update Process:
  1. Payment #1 amount: ₱4,000 → ₱6,000
  2. Payment #1 method: GCash → Cash
  3. Recalculate booking:
     - amount_paid = ₱6,000
     - remaining_balance = ₱4,000
     - payment_status = 'partial'
     - status = 'confirmed'

Result:
  - STILL only 1 payment (Payment #1)
  - Now shows: ₱6,000 Cash (updated)
  - NOT 2 payments!
```

---

#### Step 3: After 5 Minutes (Edit Option Gone)
```
Guest wants to pay more

Options:
  ❌ Can't edit Payment #1 (more than 5 minutes old)
  ✅ Can make another payment (Payment #2)

Guest makes new payment:
  - Payment #2: ₱4,000 GCash

Result:
  - Payment #1: ₱6,000 Cash (original/edited)
  - Payment #2: ₱4,000 GCash (new)
  - Total: ₱10,000 (fully paid)
```

---

## Time Limit Logic

### Why 5 Minutes?

1. **Recent Mistakes**: Allows quick correction of input errors
2. **Prevents Abuse**: Can't edit old payments to avoid audit issues
3. **Fair Window**: Enough time to notice and fix mistakes
4. **System Integrity**: Old payments are "locked in" for accounting

### Time Calculation

```php
// Check in controller
if ($payment->created_at->diffInMinutes(now()) > 5) {
    return redirect()->route('payments.show', $payment)
        ->with('error', 'Payment can only be edited within 5 minutes of creation.');
}
```

### JavaScript Timer

```javascript
const createdAt = new Date('2025-10-22T14:30:00Z');
const expiryTime = new Date(createdAt.getTime() + 5 * 60000); // +5 minutes

function updateTimeRemaining() {
    const now = new Date();
    const diff = expiryTime - now;
    
    if (diff <= 0) {
        // Disable form
        document.getElementById('timeRemaining').textContent = 'Expired';
    } else {
        const minutes = Math.floor(diff / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);
        document.getElementById('timeRemaining').textContent = `${minutes}m ${seconds}s`;
    }
}

setInterval(updateTimeRemaining, 1000); // Update every second
```

---

## Validation Rules

### Amount Validation

```php
$request->validate([
    'payment_amount' => "required|numeric|min:{$minimumPayment}|max:{$remainingBalance}",
    'payment_method' => 'required|in:cash,card,bank_transfer,gcash,paymaya,online',
    'notes' => 'nullable|string|max:500',
]);
```

**Where**:
- `$minimumPayment` = min(50% of total, remaining balance)
- `$remainingBalance` = total - (other payments, excluding this one)

### Example:
```
Booking: ₱10,000
Payment #1: ₱3,000 (this payment being edited)
Payment #2: ₱2,000 (other payment)

Calculation:
- Other Payments: ₱2,000
- Available Balance: ₱10,000 - ₱2,000 = ₱8,000
- Minimum: min(₱5,000, ₱8,000) = ₱5,000

Validation:
- Min: ₱5,000
- Max: ₱8,000

Guest can enter: ₱5,000 to ₱8,000
```

---

## Database Impact

### When Payment is Edited:

```sql
-- BEFORE UPDATE
SELECT * FROM payments WHERE id = 25;
| id | booking_id | amount  | payment_method | created_at           |
|----|------------|---------|----------------|----------------------|
| 25 | 38         | 2500.00 | gcash          | 2025-10-22 14:30:00 |

SELECT * FROM bookings WHERE id = 38;
| id | total_price | amount_paid | remaining_balance | payment_status |
|----|-------------|-------------|-------------------|----------------|
| 38 | 5000.00     | 2500.00     | 2500.00          | partial        |

-- AFTER UPDATE (changed to ₱3,000 Cash)
SELECT * FROM payments WHERE id = 25;
| id | booking_id | amount  | payment_method | created_at           |
|----|------------|---------|----------------|----------------------|
| 25 | 38         | 3000.00 | cash          | 2025-10-22 14:30:00  | ← Same row, updated

SELECT * FROM bookings WHERE id = 38;
| id | total_price | amount_paid | remaining_balance | payment_status |
|----|-------------|-------------|-------------------|----------------|
| 38 | 5000.00     | 3000.00     | 2000.00          | partial        | ← Recalculated
```

**Key Point**: Same payment ID (25) - UPDATED, not new row!

---

## Security & Authorization

### Checks Performed:

```php
// 1. User owns the payment
if (Auth::user()->role !== 'admin' && $payment->user_id !== Auth::id()) {
    abort(403, 'Unauthorized access to this payment.');
}

// 2. Payment is not refunded or failed
if ($payment->status === 'refunded' || $payment->status === 'failed') {
    return redirect()->with('error', 'This payment cannot be edited.');
}

// 3. Within time limit
if ($payment->created_at->diffInMinutes(now()) > 5) {
    return redirect()->with('error', 'Payment can only be edited within 5 minutes.');
}
```

---

## Logging & Audit Trail

### Update Log Entry:

```
[2025-10-22 14:35:42] local.INFO: Payment updated
{
    "payment_id": 25,
    "old_amount": 2500.00,
    "new_amount": 3000.00,
    "payment_method": "cash"
}
```

**Purpose**: Track all payment modifications for auditing.

---

## User Interface

### Confirmation Page - Within 5 Minutes:

```
┌─────────────────────────────────────────────────────────────┐
│ ✅ Payment Completed!                                       │
│ Your payment has been processed successfully.               │
├─────────────────────────────────────────────────────────────┤
│ ℹ️ Payment Recorded                                         │
│ Your payment has been recorded. You can edit this payment   │
│ within 5 minutes to change the amount or payment method.    │
├─────────────────────────────────────────────────────────────┤
│ ⚠️ Complete Your Payment                                    │
│ Remaining balance: ₱2,500.00                                │
│                                                             │
│ [🔵 Edit This Payment]      [🟡 Make Another Payment]       │
│  Change amount or method      Add new payment               │
├─────────────────────────────────────────────────────────────┤
│ [View Booking] [Payment History] [My Bookings]              │
└─────────────────────────────────────────────────────────────┘
```

### Edit Payment Page:

```
┌─────────────────────────────────────────────────────────────┐
│ Edit Payment                          [Back to Confirmation] │
├─────────────────────────────────────────────────────────────┤
│ ⏰ Time Limit                                               │
│ Time remaining: 4m 32s                                      │
├─────────────────────────────────────────────────────────────┤
│ Booking Summary                                             │
│ Available Balance: ₱5,000.00                                │
├─────────────────────────────────────────────────────────────┤
│ ℹ️ Current: ₱2,500.00 | GCash | Oct 22 2:30 PM             │
├─────────────────────────────────────────────────────────────┤
│ Payment Amount:                                             │
│ [Minimum 50%: ₱2,500] [Maximum: ₱5,000]                     │
│ ₱ [_______]                                                 │
├─────────────────────────────────────────────────────────────┤
│ Payment Method:                                             │
│ [Cash] [Card] [GCash] [Bank Transfer]                       │
├─────────────────────────────────────────────────────────────┤
│ [Update Payment] [Cancel]                                   │
└─────────────────────────────────────────────────────────────┘
```

---

## Error Handling

### Error 1: Time Expired

**Scenario**: Guest tries to edit after 5 minutes

```
Error: "Payment can only be edited within 5 minutes of creation."
Redirect: Back to payment confirmation
```

### Error 2: Invalid Amount

**Scenario**: Guest enters amount outside min/max range

```
Validation Error: "The payment amount must be between ₱2,500 and ₱5,000."
Action: Form redisplays with error message
```

### Error 3: Payment Already Refunded

**Scenario**: Admin refunded payment, guest tries to edit

```
Error: "This payment cannot be edited."
Redirect: Back to payment show page
```

---

## Testing Scenarios

### Test 1: Edit Within Time Limit

```
1. Make payment: ₱2,500 GCash
2. Go to confirmation page
3. Verify "Edit This Payment" button is visible
4. Click "Edit This Payment"
5. Change amount to ₱3,500
6. Change method to Cash
7. Click "Update Payment"
8. Verify: Confirmation shows ₱3,500 Cash
9. Check database: Only 1 payment record (updated)
```

### Test 2: Edit Button Disappears After 5 Minutes

```
1. Make payment: ₱2,500 GCash
2. Wait 6 minutes
3. Refresh confirmation page
4. Verify: "Edit This Payment" button is GONE
5. Verify: Only "Make Another Payment" button shows
```

### Test 3: Form Auto-Disables on Edit Page

```
1. Make payment: ₱2,500 GCash
2. Click "Edit This Payment"
3. Wait on edit page for 5 minutes
4. Verify: Timer shows "Expired"
5. Verify: Form inputs are disabled
6. Verify: Submit button is disabled
```

### Test 4: Multiple Payments - Edit One

```
1. Make Payment #1: ₱3,000 Cash
2. Make Payment #2: ₱2,000 GCash (immediately)
3. Edit Payment #2 (within 5 min): Change to ₱1,500 Card
4. Verify calculation:
   - Payment #1: ₱3,000 (unchanged)
   - Payment #2: ₱1,500 (updated)
   - Total Paid: ₱4,500
   - Remaining: ₱500
```

---

## Files Modified/Created

1. ✅ **routes/web.php** - Added edit and update routes
2. ✅ **app/Http/Controllers/PaymentController.php** - Added edit() and update() methods
3. ✅ **resources/views/payments/edit.blade.php** - NEW file (edit form)
4. ✅ **resources/views/payments/confirmation.blade.php** - Updated buttons and notices

---

## Benefits

### For Guests:
- ✅ Fix typos/mistakes quickly
- ✅ Change payment method if needed
- ✅ Adjust amount without creating duplicate
- ✅ No need to contact support for minor changes

### For System:
- ✅ Cleaner payment records (no duplicates)
- ✅ Accurate payment history
- ✅ Better audit trail
- ✅ Reduced support tickets

---

## Important Notes

### What Gets Updated:
- ✅ Payment amount
- ✅ Payment method
- ✅ Payment notes
- ✅ Booking totals (amount_paid, remaining_balance)
- ✅ Booking status (if needed)

### What Does NOT Change:
- ❌ Payment reference number
- ❌ Payment creation date
- ❌ User ID
- ❌ Booking ID
- ❌ Payment ID

### Difference from "Make Another Payment":

| Feature | Edit Payment | Make Another Payment |
|---------|-------------|---------------------|
| What it does | Updates existing payment | Creates new payment |
| Time limit | 5 minutes | Anytime |
| Payment count | Same (1 payment) | Increases (2+ payments) |
| Payment ID | Unchanged | New ID |
| Use case | Fix mistakes | Add more payments |

---

## Summary

✅ **Edit payment feature implemented**  
✅ **5-minute time limit enforced**  
✅ **Live countdown timer on edit page**  
✅ **Conditional "Edit" button on confirmation**  
✅ **Updates same payment record (no duplicates)**  
✅ **Full validation and authorization**  
✅ **Comprehensive error handling**  
✅ **Audit trail logging**  

Guests can now quickly fix payment mistakes within 5 minutes of creation!

---

*Last Updated: October 22, 2025*  
*Status: Implemented and Production Ready* ✅

