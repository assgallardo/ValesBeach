# Payment Management - Complete Flow Documentation

## Overview

Complete implementation of the payment management system where:
1. **ONE card per booking** in payment management (regardless of payment count)
2. **Card shows payment status** (Partial or Completed) and amount
3. **View Details** shows all individual payment transactions
4. **Refund only available** for completed bookings

---

## Complete Flow

### Step 1: Payment Management (Card View)

**Location**: `manager.payments.index`  
**Route**: `/manager/payments`

```
┌─────────────────────────────────────────────────┐
│ 🛏️ Executive Cottage      [✅ FULLY PAID]      │ ← Badge
│ #VB38                                           │
│                                                 │
│ 👤 John Doe                                     │
│    john@example.com                             │
│                                                 │
│ 📅 Oct 27 - Oct 29, 2025 (2 nights)            │
│                                                 │
│ ╔═══════════════════════════════════════════╗  │
│ ║       PAYMENT AMOUNT                      ║  │
│ ║         ₱5,000.00                         ║  │ ← Amount paid
│ ║       of ₱5,000.00                        ║  │ ← Total cost
│ ╚═══════════════════════════════════════════╝  │
│                                                 │
│ ╔═══════════════════════════════════════════╗  │
│ ║    ✅ PAYMENT COMPLETED                   ║  │ ← Status
│ ╚═══════════════════════════════════════════╝  │
│                                                 │
│     [3 Payments]           [Completed]          │ ← Metadata
│                                                 │
│        [📋 View Details]                        │ ← Click here
│        [↩️  Process Refund]                     │ ← Only if completed
└─────────────────────────────────────────────────┘
```

**Key Points**:
- ONE card shows the booking
- Payment amount is prominent
- Status (PARTIAL / COMPLETED) is clear
- Number of payments shown
- "View Details" button present

---

### Step 2: Click "View Details"

**Action**: Manager clicks `[📋 View Details]` button  
**Redirects to**: `manager.bookings.show` (Booking Details Page)  
**Route**: `/manager/bookings/{id}`

---

### Step 3: Booking Details (Full Payment Transactions)

**Location**: `manager.bookings.show`  
**Route**: `/manager/bookings/{id}`

```
╔═════════════════════════════════════════════════╗
║           Booking Details - #VB38               ║
╠═════════════════════════════════════════════════╣
║                                                 ║
║ [Booking Information]                           ║
║ - Check-in: Oct 27, 2025                        ║
║ - Check-out: Oct 29, 2025                       ║
║ - Guests: 2                                     ║
║ - Total Price: ₱5,000.00                        ║
║                                                 ║
╠═════════════════════════════════════════════════╣
║       Payment Transactions [Paid]               ║
╠═════════════════════════════════════════════════╣
║                                                 ║
║ Payment Summary:                                ║
║ ┌──────────┬──────────┬──────────┐            ║
║ │  Total   │   Paid   │ Remaining │            ║
║ │ ₱5,000   │  ₱5,000  │    ₱0    │            ║
║ └──────────┴──────────┴──────────┘            ║
║                                                 ║
║ Payment History (3 payments):                   ║
║                                                 ║
║ ┌─────────────────────────────────────────┐   ║
║ │ ₱2,000.00          [Completed]          │   ║ ← Payment 1
║ │ PAY-101                                 │   ║
║ │ Method: GCash                           │   ║
║ │ Date: Oct 20, 2025 2:00 PM              │   ║
║ │ Paid by: John Doe                       │   ║
║ └─────────────────────────────────────────┘   ║
║                                                 ║
║ ┌─────────────────────────────────────────┐   ║
║ │ ₱2,000.00          [Completed]          │   ║ ← Payment 2
║ │ PAY-102                                 │   ║
║ │ Method: Cash                            │   ║
║ │ Date: Oct 21, 2025 3:30 PM              │   ║
║ │ Paid by: John Doe                       │   ║
║ └─────────────────────────────────────────┘   ║
║                                                 ║
║ ┌─────────────────────────────────────────┐   ║
║ │ ₱1,000.00          [Completed]          │   ║ ← Payment 3
║ │ PAY-103                                 │   ║
║ │ Method: Credit Card                     │   ║
║ │ Date: Oct 22, 2025 10:15 AM             │   ║
║ │ Paid by: John Doe                       │   ║
║ │ Transaction ID: TXN-9876543             │   ║
║ └─────────────────────────────────────────┘   ║
║                                                 ║
╚═════════════════════════════════════════════════╝
```

**Key Points**:
- Shows ALL payment transactions
- Each payment has its own card
- Payment details: amount, method, date, time, user
- Transaction IDs and notes visible
- Payment summary at top (Total, Paid, Remaining)
- Payment status badge

---

## Payment Status Update Flow

### Scenario: Guest Makes First Payment (Partial)

#### Before:
```
Booking: #VB42
Total: ₱10,000
Paid: ₱0
Status: Pending
Payment Status: Unpaid
```

#### Guest pays ₱6,000 (60% - partial payment)

#### After (Payment Management Card):
```
┌─────────────────────────────────────────────────┐
│ 🛏️ Deluxe Suite          [⚠️  PARTIAL]         │
│ #VB42                                           │
│                                                 │
│ ╔═══════════════════════════════════════════╗  │
│ ║       PAYMENT AMOUNT                      ║  │
│ ║         ₱6,000.00                         ║  │ ← UPDATED!
│ ║       of ₱10,000.00                       ║  │
│ ║  ─────────────────────────────────────    ║  │
│ ║       REMAINING BALANCE                   ║  │
│ ║         ₱4,000.00                         ║  │ ← Shows remaining
│ ╚═══════════════════════════════════════════╝  │
│                                                 │
│ ╔═══════════════════════════════════════════╗  │
│ ║    ⚠️  PARTIALLY PAID                     ║  │ ← UPDATED!
│ ╚═══════════════════════════════════════════╝  │
│                                                 │
│     [1 Payment]            [Confirmed]          │ ← UPDATED!
│                                                 │
│        [📋 View Details]                        │
│        [🚫 Refund Unavailable]                  │ ← Disabled (not completed)
└─────────────────────────────────────────────────┘
```

#### View Details Shows:
```
Payment Summary:
Total: ₱10,000 | Paid: ₱6,000 | Remaining: ₱4,000

Payment History (1 payment):
┌─────────────────────────────────┐
│ ₱6,000.00    [Completed]        │
│ PAY-201                         │
│ Method: GCash                   │
│ Date: Oct 22, 2025 11:00 AM     │
└─────────────────────────────────┘
```

---

### Scenario: Guest Makes Second Payment (Completes Payment)

#### Before:
```
Booking: #VB42
Total: ₱10,000
Paid: ₱6,000
Remaining: ₱4,000
Status: Confirmed
Payment Status: Partial
```

#### Guest pays remaining ₱4,000

#### After (Payment Management Card):
```
┌─────────────────────────────────────────────────┐
│ 🛏️ Deluxe Suite          [✅ FULLY PAID]       │ ← UPDATED!
│ #VB42                                           │
│                                                 │
│ ╔═══════════════════════════════════════════╗  │
│ ║       PAYMENT AMOUNT                      ║  │
│ ║         ₱10,000.00                        ║  │ ← UPDATED!
│ ║       of ₱10,000.00                       ║  │
│ ╚═══════════════════════════════════════════╝  │ ← No remaining!
│                                                 │
│ ╔═══════════════════════════════════════════╗  │
│ ║    ✅ PAYMENT COMPLETED                   ║  │ ← UPDATED!
│ ╚═══════════════════════════════════════════╝  │
│                                                 │
│     [2 Payments]           [Completed]          │ ← UPDATED!
│                                                 │
│        [📋 View Details]                        │
│        [↩️  Process Refund]                     │ ← ENABLED!
└─────────────────────────────────────────────────┘
```

#### View Details Shows:
```
Payment Summary:
Total: ₱10,000 | Paid: ₱10,000 | Remaining: ₱0

Payment History (2 payments):
┌─────────────────────────────────┐
│ ₱4,000.00    [Completed]        │ ← Most recent
│ PAY-202                         │
│ Method: Cash                    │
│ Date: Oct 23, 2025 2:00 PM      │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ ₱6,000.00    [Completed]        │ ← Previous
│ PAY-201                         │
│ Method: GCash                   │
│ Date: Oct 22, 2025 11:00 AM     │
└─────────────────────────────────┘
```

---

## Refund Functionality

### When Refund is Available

**Condition**: `booking->status === 'completed'` AND `amount_paid > 0`

#### Example (Completed Booking):
```
Booking Status: Completed
Payment Status: Paid
Amount Paid: ₱10,000

Card shows:
[↩️  Process Refund]  ← ENABLED (yellow button)
```

**Why?**: Guest has checked out, service was rendered, refund is appropriate.

---

### When Refund is NOT Available

**Conditions**:
- Booking status = pending / confirmed / cancelled
- No payments made

#### Example (Confirmed Booking):
```
Booking Status: Confirmed
Payment Status: Partial
Amount Paid: ₱6,000

Card shows:
[🚫 Refund Unavailable]  ← DISABLED (gray button)
Tooltip: "Refund only available for completed bookings"
```

**Why?**: Guest hasn't checked in/out yet. Should cancel booking instead.

---

## Data Flow

### 1. Payment Creation
```
Guest makes payment
    ↓
PaymentController@store
    ↓
Payment record created (status: 'completed')
    ↓
Booking::updatePaymentTracking()
    ↓
Updates: amount_paid, remaining_balance, payment_status
    ↓
Updates booking status (confirmed/completed)
    ↓
Redirect to confirmation
```

### 2. Payment Management View
```
Manager visits /manager/payments
    ↓
PaymentController@index (Manager)
    ↓
Query: Bookings with payments grouped
    ↓
Load: room, user, payments relationships
    ↓
Display: One card per booking
    ↓
Shows: Payment amount, status, count
```

### 3. View Details
```
Manager clicks "View Details"
    ↓
Redirect to /manager/bookings/{id}
    ↓
ManagerBookingsController@show
    ↓
Load: user, room, services, payments.user
    ↓
Display: Booking details + all payment transactions
    ↓
Shows: Payment summary, transaction list
```

---

## Database Updates

### Payment Tracking Columns (bookings table):
```sql
amount_paid DECIMAL(10,2) DEFAULT 0
remaining_balance DECIMAL(10,2) DEFAULT 0
payment_status VARCHAR(20) DEFAULT 'unpaid'
```

### Values:
- **amount_paid**: Sum of completed payments
- **remaining_balance**: total_price - amount_paid
- **payment_status**: 'unpaid', 'partial', or 'paid'

### Updated by:
- `Booking::updatePaymentTracking()` method
- Called after each payment

---

## Controller Methods

### ManagerPaymentController@index
```php
public function index(Request $request)
{
    // Get bookings with payments grouped
    $query = Booking::with(['room', 'user', 'payments'])
        ->whereHas('payments');
    
    // Apply filters...
    
    $bookings = $query->orderBy('created_at', 'desc')->paginate(15);
    
    return view('manager.payments.index', compact('bookings', ...));
}
```

**Returns**: Bookings (not individual payments)  
**Each booking**: Contains all its payments  
**Display**: One card per booking  

---

### ManagerBookingsController@show
```php
public function show(Booking $booking)
{
    $booking->load(['user', 'room', 'services', 'payments.user']);
    return view('manager.bookings.show', compact('booking'));
}
```

**Returns**: Single booking with all relationships  
**Payments**: All payment transactions loaded  
**Display**: Full payment history  

---

## View Files

### Payment Management Card
**File**: `resources/views/manager/payments/index.blade.php`

**Displays**:
- Room name and booking reference
- Guest information
- Booking dates
- **Payment amount** (prominent)
- **Payment status** (PARTIAL / COMPLETED)
- Remaining balance (if partial)
- Payment count
- Booking status
- View Details button
- Refund button (conditional)

---

### Booking Details with Payments
**File**: `resources/views/manager/bookings/show.blade.php`

**Displays**:
- Booking information
- Guest information
- Room information
- **Payment Transactions Section**:
  - Payment summary (Total, Paid, Remaining)
  - Payment status badge
  - All individual payments with details:
    - Amount
    - Reference number
    - Payment method (with icon)
    - Date and time
    - Paid by (user name)
    - Transaction ID (if available)
    - Notes (if any)
    - Status badge

---

## Key Features Summary

✅ **One Card Per Booking** (payment management)  
✅ **Payment Amount Prominent** (on card)  
✅ **Payment Status Clear** (PARTIAL / COMPLETED)  
✅ **All Payments in View** (booking details)  
✅ **Refund Only for Completed** (conditional button)  
✅ **Real-time Updates** (payment tracking)  
✅ **Multiple Payments Support** (all grouped)  
✅ **Clear Visual Hierarchy** (important info stands out)  

---

## Testing Scenarios

### Test 1: Single Partial Payment
1. Create booking for ₱10,000
2. Guest pays ₱6,000 (partial)
3. Check payment management:
   - [ ] One card shows booking
   - [ ] Shows "₱6,000.00 of ₱10,000.00"
   - [ ] Shows "PARTIALLY PAID" status
   - [ ] Shows "Remaining: ₱4,000"
   - [ ] Shows "1 Payment" badge
   - [ ] Refund button disabled
4. Click "View Details":
   - [ ] Shows payment summary
   - [ ] Shows 1 payment transaction
   - [ ] Transaction shows ₱6,000

### Test 2: Multiple Payments (Completion)
1. From Test 1, guest pays remaining ₱4,000
2. Check payment management:
   - [ ] Same card (not new card)
   - [ ] Shows "₱10,000.00 of ₱10,000.00"
   - [ ] Shows "PAYMENT COMPLETED"
   - [ ] No remaining balance shown
   - [ ] Shows "2 Payments" badge
3. Update booking status to "completed"
4. Check payment management:
   - [ ] Refund button now enabled
5. Click "View Details":
   - [ ] Shows payment summary
   - [ ] Shows 2 payment transactions
   - [ ] Payments in chronological order

### Test 3: Three Partial Payments
1. Create booking for ₱15,000
2. Guest pays ₱5,000 (payment 1)
3. Guest pays ₱5,000 (payment 2)
4. Guest pays ₱5,000 (payment 3)
5. Check payment management:
   - [ ] One card throughout
   - [ ] Card updates after each payment
   - [ ] Final shows "₱15,000 of ₱15,000"
   - [ ] Shows "3 Payments" badge
6. Click "View Details":
   - [ ] Shows all 3 payments
   - [ ] Each payment clearly separated
   - [ ] Payment summary correct

### Test 4: Refund Conditional Logic
1. Create completed booking (status: completed, paid: ₱5,000)
2. Check payment management:
   - [ ] Refund button enabled (yellow)
3. Change booking status to "confirmed"
4. Refresh payment management:
   - [ ] Refund button disabled (gray)
5. Change back to "completed"
6. Refresh payment management:
   - [ ] Refund button enabled again

---

## Files Modified

1. ✅ **app/Http/Controllers/Manager/PaymentController.php**
   - `index()`: Groups by booking instead of individual payments
   
2. ✅ **resources/views/manager/payments/index.blade.php**
   - Card-based design
   - Payment amount prominent
   - Payment status alert
   - Conditional refund button
   - Refund modal
   
3. ✅ **app/Http/Controllers/ManagerBookingsController.php**
   - `show()`: Loads payments relationship
   
4. ✅ **resources/views/manager/bookings/show.blade.php**
   - Added Payment Transactions section
   - Payment summary
   - All payment transactions displayed

---

## Architecture

```
Payment Management (List)
    ↓
┌─────────────────────────┐
│ Card 1: Booking VB38    │ ← ONE CARD
│ - Payment Amount        │
│ - Payment Status        │
│ - [View Details]        │ ← Click here
└─────────────────────────┘
    ↓
Booking Details Page
    ↓
┌──────────────────────────────┐
│ Booking Information          │
├──────────────────────────────┤
│ Payment Transactions         │ ← ALL PAYMENTS
│ ┌──────────────────────────┐ │
│ │ Payment 1: ₱2,000        │ │
│ └──────────────────────────┘ │
│ ┌──────────────────────────┐ │
│ │ Payment 2: ₱2,000        │ │
│ └──────────────────────────┘ │
│ ┌──────────────────────────┐ │
│ │ Payment 3: ₱1,000        │ │
│ └──────────────────────────┘ │
└──────────────────────────────┘
```

---

## Summary

**Payment Management**:
- ✅ ONE card per booking
- ✅ Updates with each payment
- ✅ Shows current payment amount
- ✅ Shows payment status (partial/completed)
- ✅ Refund only for completed bookings

**View Details** (Booking Page):
- ✅ Shows ALL payment transactions
- ✅ Each payment fully detailed
- ✅ Payment summary at top
- ✅ Chronological order

**Data Integrity**:
- ✅ Real-time payment tracking
- ✅ Automatic status updates
- ✅ Correct calculations

---

*Last Updated: October 22, 2025*  
*Status: Complete and Production Ready* ✅

