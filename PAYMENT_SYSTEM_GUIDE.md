# Payment System Guide - Full & Partial Payments

## Overview
The ValesBeach Resort booking system now supports **full and partial payments** with automatic status management based on payment completion.

---

## Payment Options

### 1. Full Payment (100%)
- **Amount**: Pay the entire booking amount
- **Booking Status**: `completed` (Green badge)
- **Label**: "Completed - Fully Paid"
- **Color**: Green background with white text
- **Remaining Balance**: ₱0.00

### 2. Partial Payment (50% or more)
- **Amount**: Pay at least 50% of the total booking amount
- **Booking Status**: `confirmed` (Yellow badge)
- **Label**: "Confirmed - Partial Payment"
- **Color**: Yellow background with black text
- **Remaining Balance**: Displayed in yellow with amount shown

---

## Key Features

### Payment Creation Page (`/payments/create`)
1. **Payment Info Box**: Clearly explains both payment options
2. **Quick Action Buttons**:
   - "50% (Minimum)" - Sets payment to minimum required amount
   - "Full Payment" - Sets payment to total remaining balance
3. **Dynamic Status Indicator**: Shows what status the booking will receive after payment
4. **Real-time Summary**: Updates automatically as you change the payment amount

### Payment Confirmation Page (`/payments/confirmation`)
1. **Booking Status Badge**: Shows current booking status with color coding
2. **Status Alerts**:
   - **Partial Payment Alert** (Yellow): Shows remaining balance and encourages full payment
   - **Full Payment Confirmation** (Green): Confirms booking is completed
3. **Quick Action Buttons**:
   - "View Booking" - Go to booking details
   - "Make Another Payment" - Only shown if balance remains
   - "My Bookings" - Return to bookings list

### Payment History Page (`/payments/history`)
1. **Booking Status Badges**: Each payment shows the current booking status
2. **Remaining Balance Indicator**: Yellow badge shows outstanding balance if any
3. **Visual Status Icons**:
   - Completed: Green with check-circle icon
   - Confirmed: Yellow with exclamation-circle icon
   - Pending: Gray with clock icon
   - Cancelled: Red with times-circle icon

---

## Technical Implementation

### Controller Logic (`PaymentController.php`)

#### Payment Storage
```php
public function store(Request $request, Booking $booking)
{
    // Validates minimum 50% payment
    $minimumPayment = max(1, floor($booking->total_price * 0.5));
    
    // Updates booking status based on payment amount:
    // - Full payment (100%) → status: 'completed'
    // - Partial payment (50%+) → status: 'confirmed'
}
```

#### Status Update Logic
```php
if ($request->payment_method === 'cash') {
    $totalPaid = $booking->payments()->where('status', 'completed')->sum('amount');
    $isFullyPaid = $totalPaid >= $booking->total_price;
    
    if ($isFullyPaid) {
        $booking->update(['status' => 'completed']);
    } elseif ($totalPaid >= ($booking->total_price * 0.5)) {
        if ($booking->status === 'pending') {
            $booking->update(['status' => 'confirmed']);
        }
    }
}
```

### Model Methods (`Booking.php`)

#### Payment Tracking
```php
public function updatePaymentTracking()
{
    $totalPaid = $this->payments()->where('status', 'completed')->sum('amount');
    $remainingBalance = max(0, $this->total_price - $totalPaid);
    
    $paymentStatus = 'unpaid';
    if ($totalPaid >= $this->total_price) {
        $paymentStatus = 'paid';
    } elseif ($totalPaid > 0) {
        $paymentStatus = 'partial';
    }
    
    $this->update([
        'amount_paid' => $totalPaid,
        'remaining_balance' => $remainingBalance,
        'payment_status' => $paymentStatus
    ]);
}
```

---

## Status Color Coding

### Booking Status Colors
| Status | Background | Text Color | Icon |
|--------|-----------|------------|------|
| Completed | Green (#16a34a) | White | check-circle |
| Confirmed | Yellow (#eab308) | Black | exclamation-circle |
| Pending | Gray (#6b7280) | White | clock |
| Cancelled | Red (#dc2626) | White | times-circle |

### Balance Display
- **No Balance**: Green text (₱0.00)
- **Has Balance**: Yellow text with amount
- **Balance Badge**: Yellow background with border

---

## User Flow

### Making a Payment

1. **Navigate to Payment Page**
   - From booking details, click "Make Payment"

2. **Review Booking Summary**
   - See total price, amount paid, and remaining balance

3. **Choose Payment Amount**
   - Click "50% (Minimum)" for partial payment
   - Click "Full Payment" for complete payment
   - Or enter custom amount (≥50%)

4. **Select Payment Method**
   - Cash, Card, GCash, or Bank Transfer

5. **Review Payment Summary**
   - See dynamic status indicator
   - Check remaining balance after payment

6. **Submit Payment**
   - Payment is processed
   - Booking status is updated automatically

7. **View Confirmation**
   - See payment details
   - View new booking status
   - See remaining balance (if partial payment)

### Making Additional Payments

If you made a partial payment:

1. **From Confirmation Page**
   - Click "Make Another Payment"

2. **From Booking Details**
   - Click "Make Payment" button

3. **From Payment History**
   - Click on booking to view details
   - Then click "Make Payment"

---

## Validation Rules

### Payment Amount
- **Minimum**: 50% of total booking price
- **Maximum**: Current remaining balance
- **Step**: 0.01 (allows cents)
- **Required**: Yes

### Payment Method
- **Options**: cash, card, bank_transfer, gcash, paymaya, online
- **Required**: Yes

### Notes
- **Type**: Text
- **Max Length**: 500 characters
- **Required**: No

---

## Status Workflow

```
Booking Created
     ↓
[Status: pending]
     ↓
Make Payment (50%+)
     ↓
[Status: confirmed - Partial Payment]
     ↓
Make Additional Payment (to 100%)
     ↓
[Status: completed - Fully Paid]
```

---

## Important Notes

### For Guests
1. **Minimum payment is 50%** of the total booking amount to confirm your reservation
2. **Full payment is recommended** to complete your booking immediately
3. **Partial payments** allow you to secure your booking and pay the rest later
4. **Multiple payments** are allowed until the booking is fully paid

### For Administrators
1. Booking status is **automatically updated** when payments are completed
2. **Cash payments** are marked as completed immediately
3. **Other payment methods** start as pending and need manual approval
4. Use the payment dashboard to **track and manage** all transactions

---

## Troubleshooting

### Payment is not updating booking status
- Check that payment status is "completed" (not pending)
- Verify payment amount is at least 50% of total price
- Ensure payment is properly linked to the booking

### Cannot make payment
- Check minimum payment requirement (50%)
- Verify booking is not already fully paid
- Ensure you're logged in as the booking owner

### Status shows incorrect color
- Clear browser cache
- Refresh the page
- Check that payment tracking is updated

---

## Technical Notes

### Database Fields Used
- `bookings.total_price` - Total booking amount
- `bookings.amount_paid` - Sum of completed payments
- `bookings.remaining_balance` - Calculated remaining amount
- `bookings.payment_status` - Payment completion status (paid/partial/unpaid)
- `bookings.status` - Overall booking status (completed/confirmed/pending)

### Real-time Updates
The payment form uses JavaScript to:
- Update payment summary dynamically
- Show estimated booking status
- Calculate remaining balance
- Format currency with proper separators

---

## Version Information
- **Implementation Date**: October 22, 2025
- **Minimum Payment**: 50% of total booking price
- **Status Colors**: Green (completed), Yellow (confirmed)
- **Payment Methods**: Cash, Card, GCash, Bank Transfer, PayMaya, Online

---

For any questions or issues, please contact the system administrator.

