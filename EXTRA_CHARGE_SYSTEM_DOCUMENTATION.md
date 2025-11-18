# Extra Charge System - Working Documentation

## Date: 2024-01-XX
## Status: ✅ WORKING CORRECTLY

## Overview
The extra charge system allows admin/manager users to add additional charges to customer invoices. These charges are automatically integrated into the payment tracking system.

## How It Works

### 1. Creating Extra Charges

**Method A: Individual Save (AJAX)**
- Admin clicks "+ Additionals" button on invoice generation page
- Fills in extra charge details (description, amount, etc.)
- Clicks green checkmark icon
- AJAX call to `PaymentController@saveExtraCharge`
- Payment created immediately with `payment_transaction_id`

**Method B: Batch Save**
- Admin adds multiple extra charges to invoice form
- Clicks "Generate Invoice" button
- All extra charges created at once via `PaymentController@saveCustomerInvoice`
- All payments get same `payment_transaction_id`

### 2. Database Structure

Extra charge payments have:
```php
[
    'user_id' => <customer_id>,
    'booking_id' => null,
    'service_request_id' => null,
    'food_order_id' => null,
    'payment_reference' => 'EXT-XXXXX',  // Unique identifier
    'payment_transaction_id' => 'TXN-XXXXX',  // Groups with other payments
    'amount' => <amount>,
    'payment_method' => 'cash',
    'status' => 'pending|confirmed|completed',
    'payment_details' => [
        'description' => 'User-entered description',
        'reference' => 'Optional reference',
        'details' => 'Optional additional details',
        'type' => 'extra',
        'item_type' => 'extra_charge',
        'saved_individually' => true,  // If saved via AJAX
        'invoice_id' => <invoice_id>,  // If from invoice
        'invoice_number' => 'INV-XXXXX'
    ]
]
```

### 3. Where Extra Charges Appear

#### For Guests (Customer View)

**Payment History** (`/payments/history`)
- Shows transactions where NOT all payments are completed
- Extra charges appear here if transaction has pending/confirmed payments
- Logic: Transaction excluded if ALL payments have status='completed'

**Completed Transactions** (`/payments/completed`)
- Shows transactions where ALL payments are completed
- Extra charges appear here if transaction is fully completed
- Logic: Transaction included if COUNT(*) = SUM(status='completed')

#### For Admin/Manager

**All Payments View** (`/admin/payments`)
- Shows all payments grouped by `payment_transaction_id`
- Extra charges appear in their respective transaction groups

**Customer Payment Details** (`/admin/payments/customer/{user}?transaction_id=XXX`)
- Shows all payments for specific customer transaction
- Extra charges listed alongside bookings, services, food orders

**Invoice Generation** (`/admin/payments/customer/{user}/invoice?transaction_id=XXX`)
- Shows all items in transaction including existing extra charges
- Allows adding new extra charges to the same transaction

### 4. Code Locations

#### Controllers
- `app/Http/Controllers/PaymentController.php`
  - Line 1380: `generateCustomerInvoice()` - Loads invoice with extra charges
  - Line 1542: `saveCustomerInvoice()` - Saves invoice with extra charges
  - Line 2238: `saveExtraCharge()` - AJAX endpoint for individual save
  - Line 282: `history()` - Guest payment history (filters out completed transactions)
  - Line 383: `completedTransactions()` - Guest completed transactions

#### Views
- `resources/views/invoices/customer-invoice-edit.blade.php` - Invoice generation form
- `resources/views/payments/history.blade.php` - Guest payment history
- `resources/views/payments/completed.blade.php` - Guest completed transactions
- `resources/views/admin/payments/customer.blade.php` - Admin customer payments

#### Routes
```php
// Admin routes
Route::get('/payments/customer/{user}/invoice', [PaymentController::class, 'generateCustomerInvoice'])
    ->name('payments.customer.invoice');
Route::post('/payments/customer/{user}/invoice', [PaymentController::class, 'saveCustomerInvoice'])
    ->name('payments.customer.invoice.save');
Route::post('/payments/customer/{user}/extra-charge', [PaymentController::class, 'saveExtraCharge'])
    ->name('payments.extraCharge.save');

// Guest routes
Route::get('/payments/history', [PaymentController::class, 'history'])
    ->name('payments.history');
Route::get('/payments/completed', [PaymentController::class, 'completedTransactions'])
    ->name('payments.completed');
```

## Verified Working Behavior

### Test Results (from diagnostic)
- ✅ All extra charges have `payment_transaction_id`
- ✅ Extra charges properly grouped with other payments in transaction
- ✅ Extra charges appear in guest payment history when active
- ✅ Extra charges appear in completed transactions when fully paid
- ✅ No orphaned extra charges
- ✅ No missing transaction IDs

### Example Transaction Flow

1. **Admin creates transaction**: Booking (₱7500) + Service (₱500)
   - Both get `payment_transaction_id`: `TXN-ABC123`
   - Status: pending
   - Guest sees in "Payment History"

2. **Admin adds extra charge**: Pool equipment rental (₱250)
   - Gets same `payment_transaction_id`: `TXN-ABC123`
   - Status: pending
   - Guest still sees in "Payment History"
   - Total transaction amount: ₱8250

3. **Customer pays all items**:
   - All three payments marked as completed
   - Transaction moves from "Payment History" to "Completed Transactions"
   - Guest can view full transaction including extra charge in completed view

## Potential User Confusion Points

### "Extra charges don't show up"
**Cause**: User looking in wrong view
- If transaction is active (not all completed), look in "Payment History"
- If transaction is completed, look in "Completed Transactions"
- Extra charges never appear outside their transaction group

### "Payment session conflicts"
**Cause**: Multiple browser tabs or stale data
- Solution: Refresh page before adding extra charges
- System uses transaction_id from URL parameter

### "Extra charge disappeared"
**Cause**: Transaction status changed
- When transaction completes, it moves from history to completed
- Extra charge still exists, just in different view

## Maintenance

### Diagnostic Script
Run `php diagnose_extra_charges.php` to check for issues:
- Missing `payment_transaction_id`
- Orphaned extra charges
- Proper transaction grouping

### Test Script
Run `php test_extra_charge_flow.php` to verify:
- Extra charge creation
- Transaction grouping
- Guest view visibility logic

## Conclusion

The extra charge system is **working correctly** as of this documentation. All extra charges:
- Are created with proper `payment_transaction_id`
- Appear in appropriate guest views based on transaction completion status
- Are properly grouped with other payments in the transaction
- Can be viewed by admin/manager in customer payment details

No bugs found in current implementation.
