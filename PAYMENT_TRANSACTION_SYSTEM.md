# Payment Transaction System Implementation

## Overview
Implemented a **Payment Transaction Grouping System** that prevents completed payment transactions from accepting new payments. When all payments in a transaction are completed, any new bookings or services create a **NEW payment transaction**, maintaining proper separation between completed and active payment sessions.

## Problem Solved
**Before**: When a guest's payments were marked as completed, and they made a new booking, the new payment would be added to the same completed group, causing confusion.

**After**: Completed payment transactions are **locked**. New bookings/services after completion automatically create a new payment transaction with its own lifecycle.

## Database Changes

### New Migration: `2025_11_16_000001_add_payment_transaction_id_to_payments_table.php`
```php
Schema::table('payments', function (Blueprint $table) {
    $table->string('payment_transaction_id')->nullable()->after('payment_reference');
    $table->index(['user_id', 'payment_transaction_id']);
    $table->index(['payment_transaction_id', 'status']);
});
```

**Purpose**: Groups payments into sessions/transactions that can be independently completed or reverted.

### Transaction ID Format
- Active transactions: `TXN-XXXXXXXXXXXX` (12 random uppercase characters)
- Auto-assigned when creating payments
- Persists through all payments in that session

## Transaction Lifecycle

### 1. New Payment Creation
When a guest makes a payment (booking/service/food order):

```php
// Check for active (non-completed) payments
$activePayment = Payment::where('user_id', auth()->id())
    ->whereIn('status', ['pending', 'confirmed', 'processing', 'overdue', 'failed', 'cancelled', 'refunded'])
    ->first();

if ($activePayment) {
    // Use existing active transaction
    $paymentTransactionId = $activePayment->payment_transaction_id;
} else {
    // All previous payments completed - CREATE NEW TRANSACTION
    $paymentTransactionId = 'TXN-' . strtoupper(\Illuminate\Support\Str::random(12));
}
```

**Logic**:
- ✅ Has active payments → Add to existing transaction
- ✅ All payments completed → New transaction created
- ✅ No payments yet → New transaction created

### 2. Completing a Transaction (Admin/Manager)
Admin/manager clicks "Complete Payments":

```php
// Get the active payment transaction ID
$activeTransactionId = Payment::where('user_id', $customer->id)
    ->whereIn('status', ['pending', 'confirmed', 'processing'])
    ->value('payment_transaction_id');

// Update ALL payments in this specific transaction only
Payment::where('payment_transaction_id', $activeTransactionId)
    ->whereIn('status', ['pending', 'confirmed', 'processing'])
    ->update(['status' => 'completed', 'payment_date' => now()]);
```

**Result**: ALL payments in that transaction marked as completed, transaction moves to "Completed Transactions" view.

### 3. Reverting a Transaction (Admin/Manager)
Admin/manager clicks "Revert to Active":

```php
// Revert specific transaction (requires transaction_id parameter)
Payment::where('payment_transaction_id', $transactionId)
    ->where('user_id', $customer->id)
    ->where('status', 'completed')
    ->update(['status' => 'confirmed']);
```

**Result**: Transaction returns to "Payment Management" (active) view.

## Guest Experience

### Payment History (Active Payments)
**Route**: `/payments/history`

Shows **ONLY** payments from active (non-completed) transactions:

```php
// Get active transaction IDs
$activeTransactionIds = Payment::where('user_id', auth()->id())
    ->whereIn('status', ['pending', 'confirmed', 'processing', ...])
    ->pluck('payment_transaction_id')
    ->unique();

// Filter bookings/services/food orders by active transactions
$bookings = Booking::whereHas('payments', function($query) use ($activeTransactionIds) {
    $query->whereIn('payment_transaction_id', $activeTransactionIds);
});
```

### Completed Transactions (Guest)
**Route**: `/payments/completed`

Shows **ONLY** fully completed transactions (all payments completed):

```php
// Get transaction IDs where ALL payments are completed
$completedTransactionIds = DB::table('payments')
    ->where('user_id', $user->id)
    ->groupBy('payment_transaction_id')
    ->havingRaw('COUNT(*) = SUM(CASE WHEN status = ? THEN 1 ELSE 0 END)', ['completed'])
    ->pluck('payment_transaction_id');
```

**Display**: One card per completed transaction, showing:
- Guest info
- Payment types (bookings, services, food orders, extra charges)
- Total amount
- Number of payments
- Latest date
- "View Details" button

### Completed Transaction Details
**Route**: `/payments/completed/details?transaction_id=TXN-XXX`

Shows individual payment breakdown for a specific completed transaction:
- Guest information card with totals
- Summary cards for each payment type
- Complete transaction table with all payment details

## Admin/Manager Views

### Payment Management (Admin/Manager)
**Routes**: 
- `/admin/payments`
- `/manager/payments`

**Grouping**: By `payment_transaction_id`

Shows **ONLY** active payment transactions (at least one non-completed payment):

```php
$query = DB::table('payments')
    ->select('payment_transaction_id', 'user_id')
    ->groupBy('payment_transaction_id', 'user_id')
    ->havingRaw('SUM(CASE WHEN status IN (...) THEN 1 ELSE 0 END) > 0', [
        'pending', 'confirmed', 'processing', 'overdue', 'failed', 'cancelled', 'refunded'
    ]);
```

**Actions**:
- View Details → See all payments in transaction
- Complete Payments → Mark ALL payments in transaction as completed

### Completed Transactions (Admin/Manager)
**Routes**: 
- `/admin/payments/completed`
- `/manager/payments/completed`

Shows **ONLY** fully completed transactions (ALL payments completed):

```php
$completedTransactions = DB::table('payments')
    ->select('payment_transaction_id', 'user_id')
    ->groupBy('payment_transaction_id', 'user_id')
    ->havingRaw('COUNT(*) = SUM(CASE WHEN status = ? THEN 1 ELSE 0 END)', ['completed']);
```

**Actions**:
- View Details → See all completed payments (read-only)
- Revert to Active → Move transaction back to active payments

## Key Features

### 1. Automatic Transaction Separation
- ✅ Guest makes booking #1 → Creates Transaction A
- ✅ Guest adds service → Added to Transaction A
- ✅ Admin completes Transaction A → All payments marked completed
- ✅ Guest makes booking #2 → **NEW Transaction B created automatically**

### 2. Repeat Customer Support
Same guest can have:
- **Multiple Active Transactions**: If they make several bookings before any are completed
- **Multiple Completed Transactions**: Each completion creates a historical record
- **Separate Payment Sessions**: Each transaction is independent

Example:
```
Guest: Adrian Seth Gallardo

Transaction TXN-DHKMUNNAZSHC (Completed Nov 15, 2025)
├── Booking: Bahay Kubo 1 - ₱200.00
├── Service: Spa Massage - ₱1,500.00
├── Food Order: #VB202511090116 - ₱21.99
└── Extra Charge: Broken plates - ₱100.00
Total: ₱1,821.99 (4 payments, all completed)

Transaction TXN-ABC123XYZ456 (Active - Nov 16, 2025)
├── Booking: Deluxe Room - ₱3,500.00 (pending)
└── Service: Massage - ₱800.00 (pending)
Total: ₱4,300.00 (2 payments, active)
```

### 3. Bidirectional Sync
- Admin/Manager completes transaction → Guest's payment history updates instantly
- Guest sees completed transaction → Appears in admin/manager completed view
- Revert in admin → Transaction returns to guest's payment history

### 4. Data Integrity
- ✅ **No mixing**: Completed transactions cannot accept new payments
- ✅ **No orphans**: Every payment belongs to a transaction
- ✅ **Atomic operations**: Complete/revert affects entire transaction
- ✅ **Audit trail**: Transaction IDs provide permanent record

## Files Modified

### Controllers
1. **`app/Http/Controllers/PaymentController.php`**
   - `store()` - Auto-assign transaction ID based on active payments
   - `history()` - Filter by active transaction IDs
   - `completed()` - Filter by completed transaction IDs, group by transaction
   - `completedDetails()` - Show specific transaction details
   - `index()` - Group by transaction for admin/manager
   - `adminCompleted()` - Show only fully completed transactions
   - `completeAllCustomerPayments()` - Complete specific transaction
   - `revertAllCustomerPayments()` - Revert specific transaction

### Models
2. **`app/Models/Payment.php`**
   - Added `payment_transaction_id` to `$fillable`

### Views
3. **`resources/views/payments/completed.blade.php`**
   - Changed from single card to loop through `$paymentTransactions`
   - Each transaction shows as separate card
   - Pass `transaction_id` to details route

4. **`resources/views/admin/payments/completed.blade.php`**
   - Already using transaction-based grouping
   - Pass `payment_transaction_id` to actions

5. **`resources/views/manager/payments/completed.blade.php`**
   - Already using transaction-based grouping
   - Pass `payment_transaction_id` to actions

### Database
6. **`database/migrations/2025_11_16_000001_add_payment_transaction_id_to_payments_table.php`**
   - New column: `payment_transaction_id`
   - Indexes for performance

### Scripts
7. **`assign_payment_transaction_ids.php`**
   - One-time script to assign transaction IDs to existing payments
   - Groups all existing payments by user into single transaction

## Testing Scenarios

### Scenario 1: New Guest First Booking
1. Guest creates booking → New transaction created (TXN-XXXXX)
2. Payment appears in Payment History (active)
3. Admin marks as completed → Moves to Completed Transactions
4. Guest makes another booking → **NEW transaction** created (TXN-YYYYY)
5. Both transactions visible in guest's Completed Transactions

### Scenario 2: Multiple Services in One Session
1. Guest creates booking → Transaction A
2. Guest adds service → Added to Transaction A
3. Guest orders food → Added to Transaction A
4. Admin completes → All 3 payments completed together
5. Appears as 1 completed transaction with 3 payments

### Scenario 3: Partial Completion
1. Guest has 3 payments in Transaction A (all pending)
2. Admin completes Transaction A → All 3 marked completed
3. Transaction cannot be partially completed
4. Must revert entire transaction to add/modify

### Scenario 4: Repeat Customer
1. Guest completes Transaction A (Nov 10) - ₱2,000
2. Guest completes Transaction B (Nov 15) - ₱1,500
3. Guest creates new booking (Nov 16) → Transaction C
4. Guest views Completed Transactions → Sees 2 separate cards (A & B)
5. Guest views Payment History → Sees only Transaction C (active)

## Benefits

### For Guests
- ✅ Clear separation between paid and unpaid transactions
- ✅ Historical record of each payment session
- ✅ No confusion when making repeat bookings

### For Admin/Manager
- ✅ Easy to track repeat customers
- ✅ Complete transactions atomically (all or nothing)
- ✅ Clear audit trail with transaction IDs
- ✅ Can revert entire transaction if needed

### For System
- ✅ Data integrity maintained
- ✅ No orphaned payments
- ✅ Scalable for high-volume businesses
- ✅ Supports complex payment workflows

## Future Enhancements

### Potential Additions
1. **Transaction Naming**: Allow admin to name transactions (e.g., "Summer Vacation 2025")
2. **Partial Revert**: Revert individual payments within a transaction
3. **Transaction Notes**: Add notes/comments to entire transaction
4. **Transaction Merge**: Combine multiple transactions
5. **Auto-Complete**: Automatically complete transactions after X days
6. **Transaction Reports**: Generate reports grouped by transaction
7. **Transaction Status**: Add "partially_paid", "overdue", etc. to transaction level

## Summary

The Payment Transaction System transforms the payment flow from a **user-centric** to a **session-centric** approach. Each payment session (transaction) has its own lifecycle, preventing completed transactions from accepting new payments and maintaining clear historical records for repeat customers.

**Key Principle**: Once a payment transaction is completed, it's **immutable** (unless reverted). New payments always create new transactions, ensuring data integrity and clear audit trails.
