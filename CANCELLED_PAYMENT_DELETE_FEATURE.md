# Cancelled Payment Delete Feature Implementation

## Overview
Added the ability to delete cancelled payment transactions from the admin and manager payment management views.

## Implementation Details

### 1. Routes Added
**File:** `routes/web.php`

**Admin Route:**
```php
Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
```
- Full route: `DELETE admin/payments/{payment}`
- Route name: `admin.payments.destroy`
- Middleware: `auth, user.status, role:admin`

**Manager Route:**
```php
Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
```
- Full route: `DELETE manager/payments/{payment}`
- Route name: `manager.payments.destroy`
- Middleware: `auth, user.status, role:manager,admin,staff`

### 2. Controller Method
**File:** `app/Http/Controllers/PaymentController.php`

**Method:** `destroy(Payment $payment)`

**Authorization:**
- Only accessible to admin, manager, and staff roles
- Returns 403 error if unauthorized

**Validation:**
- Only allows deletion of payments with status === 'cancelled'
- Returns 400 error if payment is not cancelled

**Functionality:**
- Deletes the payment record from database
- Logs deletion with payment ID, reference, and deleted_by user ID
- Returns JSON response for AJAX handling

**Success Response:**
```json
{
    "success": true,
    "message": "Cancelled payment transaction deleted successfully.",
    "payment_id": 123
}
```

**Error Responses:**
```json
// Unauthorized
{
    "success": false,
    "message": "Unauthorized access."
}

// Not cancelled
{
    "success": false,
    "message": "Only cancelled payment transactions can be deleted."
}

// Server error
{
    "success": false,
    "message": "Failed to delete payment: [error message]"
}
```

### 3. Frontend UI Changes

#### Admin View
**File:** `resources/views/admin/payments/customer.blade.php`

**Delete Button (Conditional):**
```php
@if($payment->status === 'cancelled')
<button type="button" 
        class="btn btn-sm btn-danger"
        onclick="deleteCancelledPayment({{ $payment->id }}, '{{ $payment->payment_reference }}', this)"
        title="Delete Cancelled Payment">
    <i class="fas fa-trash"></i> Delete
</button>
@endif
```

**JavaScript Function:**
```javascript
function deleteCancelledPayment(paymentId, paymentRef, button) {
    if (!confirm(`Are you sure you want to delete this cancelled transaction (${paymentRef})? This action cannot be undone.`)) {
        return;
    }

    const row = button.closest('tr');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

    fetch(`/admin/payments/${paymentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fade out and remove the row
            row.style.transition = 'opacity 0.3s';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
            
            // Show success message
            alert(data.message);
        } else {
            alert('Error: ' + data.message);
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-trash"></i> Delete';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the payment.');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-trash"></i> Delete';
    });
}
```

#### Manager View
**File:** `resources/views/manager/payments/customer.blade.php`

Identical implementation to admin view, but uses:
- Route: `/manager/payments/${paymentId}` instead of `/admin/payments/${paymentId}`
- Same conditional display, same JavaScript function

## User Flow

### Step 1: View Payment Transaction
- Admin/Manager navigates to Payment Management
- Views customer payment transactions grouped by `payment_transaction_id`

### Step 2: Identify Cancelled Transaction
- Payments with status "Cancelled" will display a red "Delete" button
- Button appears next to the "View" button in Actions column

### Step 3: Delete Cancelled Transaction
- Click the "Delete" button
- Confirmation dialog appears: "Are you sure you want to delete this cancelled transaction (PAY-XXXXXX)? This action cannot be undone."
- Click "OK" to confirm or "Cancel" to abort

### Step 4: Processing
- Button changes to "Deleting..." with spinner icon
- AJAX DELETE request sent to backend
- Backend validates:
  - User is authorized (admin/manager/staff)
  - Payment status is 'cancelled'

### Step 5: Success/Error Handling
**Success:**
- Row fades out with 0.3s transition
- Row is removed from table
- Success alert: "Cancelled payment transaction deleted successfully."

**Error:**
- Alert displays error message
- Button returns to original state
- Row remains in table

## Security Features

1. **Route Protection:** Middleware ensures only authenticated admin/manager/staff can access
2. **Status Validation:** Only cancelled payments can be deleted (hard-coded in controller)
3. **CSRF Protection:** All requests require valid CSRF token
4. **Logging:** All deletions logged with payment ID, reference, and deleting user ID
5. **Confirmation Dialog:** Prevents accidental deletions

## Testing Checklist

- [ ] Admin can see delete button for cancelled payments
- [ ] Manager can see delete button for cancelled payments
- [ ] Staff can see delete button for cancelled payments
- [ ] Delete button does NOT appear for non-cancelled payments (pending, completed, failed)
- [ ] Confirmation dialog appears when delete button is clicked
- [ ] Clicking "Cancel" in dialog aborts deletion
- [ ] Clicking "OK" in dialog sends DELETE request
- [ ] Unauthorized users (guests, customers) cannot access delete route
- [ ] Non-cancelled payments cannot be deleted (returns 400 error)
- [ ] Successful deletion removes row from UI
- [ ] Successful deletion removes record from database
- [ ] Deletion is logged in Laravel logs
- [ ] Error handling works (network error, server error, validation error)
- [ ] Button state updates correctly during processing
- [ ] CSRF token is properly sent in request headers

## Database Impact

**Before Deletion:**
- Payment record exists in `payments` table with status='cancelled'

**After Deletion:**
- Payment record is permanently removed from `payments` table
- No cascade effects (payment snapshot system preserves history elsewhere if needed)
- Action is logged in Laravel application logs

## Notes

- This feature only affects **cancelled** payments, ensuring data integrity for completed/pending transactions
- Delete is permanent - there is no "soft delete" or recovery mechanism
- Payments with active status (pending, completed) are protected and cannot be deleted
- The snapshot system (implemented separately) ensures related entity deletions don't affect payment history
- This delete function is for cleaning up cancelled transactions that are no longer needed

## Related Files

1. `routes/web.php` - Route definitions
2. `app/Http/Controllers/PaymentController.php` - destroy() method
3. `resources/views/admin/payments/customer.blade.php` - Admin UI
4. `resources/views/manager/payments/customer.blade.php` - Manager UI

## Logs Example

```
[2025-01-16 10:30:45] local.INFO: Cancelled payment deleted  
{
    "payment_id": 123,
    "payment_reference": "PAY-20250116-ABC123",
    "deleted_by": 1
}
```

## Deployment Notes

- Run `php artisan route:clear` after deploying
- Run `php artisan config:clear` after deploying
- Run `php artisan cache:clear` after deploying
- No database migrations required (feature uses existing table structure)
- No new dependencies required
