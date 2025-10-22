# Payment Form Troubleshooting Guide

## Issue: Form Not Submitting to Confirmation Page

If the payment form doesn't proceed to the confirmation page after clicking "Process Payment", follow these steps:

---

## Quick Checks

### 1. **Check Browser Console**
   - Open browser developer tools (F12)
   - Go to Console tab
   - Look for any JavaScript errors (red text)
   - Look for these log messages:
     ```
     Form submitting...
     Payment Amount: X
     Payment Method: Y
     Form validation passed, submitting...
     ```

### 2. **Check for Alerts**
   The form now shows alerts if:
   - ❌ **No payment method selected**: "Please select a payment method"
   - ❌ **Invalid amount**: "Please enter a valid payment amount between..."

### 3. **Check for Error Messages**
   After clicking submit, look for a **red error box** at the top of the form showing:
   - Validation errors
   - Server errors
   - Database errors

---

## Common Issues & Solutions

### ❌ **Issue 1: Payment Method Not Selected**

**Symptom**: Alert says "Please select a payment method"

**Solution**:
1. Click one of the 4 payment method cards:
   - 💵 Cash
   - 💳 Card
   - 📱 GCash
   - 🏛️ Bank Transfer
2. Card should glow green when selected
3. Display box above should show the selected method

**How to verify**:
- Selected card has green border and background
- Display box shows: "Selected Payment Method: [Method Name]"
- Browser console shows: `Payment Method: cash` (or card, gcash, bank_transfer)

---

### ❌ **Issue 2: Payment Amount Invalid**

**Symptom**: Alert says "Please enter a valid payment amount between..."

**Solution**:
1. Check the minimum amount (shown below input): **50% of booking total**
2. Check the maximum amount: **Remaining balance**
3. Enter amount between these values

**Quick Fix**:
- Click **"Partial (50%)"** button to auto-fill minimum
- Click **"Full Payment"** button to auto-fill maximum

---

### ❌ **Issue 3: Validation Errors**

**Symptom**: Red error box appears at top of form

**Possible Errors**:

1. **"The payment amount field is required."**
   - Enter an amount in the payment field

2. **"The payment amount must be at least [X]."**
   - Enter at least 50% of booking total
   - Use the "Partial (50%)" button

3. **"The payment amount may not be greater than [X]."**
   - You're trying to pay more than remaining balance
   - Use the "Full Payment" button

4. **"The payment method field is required."**
   - Select one of the 4 payment method cards

5. **"The selected payment method is invalid."**
   - Only use: cash, card, gcash, or bank_transfer
   - Click one of the displayed cards

---

### ❌ **Issue 4: Server/Database Error**

**Symptom**: Red error box says "Payment processing failed..."

**Steps to Debug**:

1. **Check Laravel Logs**:
   ```
   storage/logs/laravel.log
   ```
   Look for recent errors with "Payment processing failed"

2. **Common Database Issues**:
   - Missing `payments` table
   - Missing columns in payments table
   - Database connection issue

3. **Check Database**:
   ```powershell
   php artisan migrate:status
   ```
   All migrations should show "Ran"

4. **Check Booking Status**:
   - Make sure booking exists
   - Make sure booking belongs to logged-in user
   - Check booking has valid total_price

---

### ❌ **Issue 5: JavaScript Errors**

**Symptom**: Form doesn't validate, console shows errors

**Common Errors**:
- `Cannot read property 'value' of null` → Element not found
- `Uncaught ReferenceError` → Function not defined

**Solution**:
1. Clear browser cache (Ctrl+F5)
2. Check if page loaded completely
3. Disable browser extensions
4. Try different browser

---

## Testing the Form

### Test 1: Partial Payment (50%)
```
1. Click "Partial (50%)" button
2. Click "Cash" payment method
3. Click "Process Payment"
4. Should redirect to confirmation page
5. Remaining balance should be yellow with 50% remaining
```

### Test 2: Full Payment
```
1. Click "Full Payment" button
2. Click "Card" payment method
3. Click "Process Payment"
4. Should redirect to confirmation page
5. Remaining balance should be green (₱0.00)
```

### Test 3: Custom Amount
```
1. Type custom amount (between 50% and 100%)
2. Click "GCash" payment method
3. Click "Process Payment"
4. Should redirect to confirmation page
5. Remaining balance shown in yellow
```

---

## Debug Checklist

Use this checklist to verify everything:

### Before Clicking Submit:
- [ ] Amount is entered (shows in large input field)
- [ ] Amount is ≥ minimum (50%)
- [ ] Amount is ≤ maximum (remaining balance)
- [ ] Payment method is selected (card glows green)
- [ ] Selected method shows in display box above cards
- [ ] Summary shows correct calculations
- [ ] Status indicator is yellow or green (not red)

### After Clicking Submit:
- [ ] No alert popup appears
- [ ] No red error box appears
- [ ] Browser console shows "Form validation passed, submitting..."
- [ ] Page redirects to confirmation
- [ ] Confirmation shows payment details
- [ ] Confirmation shows selected payment method
- [ ] Confirmation shows remaining balance

---

## Console Commands to Help Debug

### Check Routes
```powershell
php artisan route:list | Select-String "payment"
```
Should show:
- `POST bookings/{booking}/payment → payments.store`
- `GET payments/confirmation/{payment} → payments.confirmation`

### Check Database
```powershell
php artisan tinker
```
Then in tinker:
```php
\App\Models\Payment::count();  // Should show number of payments
\App\Models\Booking::find(1);  // Check booking exists
```

### Check Logs
```powershell
Get-Content storage/logs/laravel.log -Tail 50
```
Look for recent errors

### Clear Cache
```powershell
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

---

## Still Not Working?

### Check Network Tab
1. Open browser developer tools (F12)
2. Go to **Network** tab
3. Click "Process Payment"
4. Look for POST request to `/bookings/{id}/payment`
5. Check response:
   - **302 Redirect** → Good! Should redirect to confirmation
   - **422 Unprocessable Entity** → Validation failed, check response tab for errors
   - **500 Internal Server Error** → Check laravel.log
   - **419 CSRF Token Mismatch** → Refresh page and try again

### Check Form Data
In Network tab, click the POST request, then:
1. Go to **Payload** or **Request** tab
2. Verify form data includes:
   ```
   _token: [CSRF token]
   payment_amount: [number]
   payment_method: cash/card/gcash/bank_transfer
   notes: [optional text]
   ```

### Enable Debug Mode
In `.env` file:
```
APP_DEBUG=true
```
Then try again and you'll see detailed error page if something fails.

**⚠️ Remember to set `APP_DEBUG=false` in production!**

---

## Expected Behavior

### Successful Submission:
1. Click "Process Payment"
2. Form validates (no alerts)
3. Page redirects to `/payments/confirmation/{payment_id}`
4. Confirmation page shows:
   - Payment reference number
   - Payment amount
   - Selected payment method with icon
   - Total booking amount
   - Total amount paid
   - Remaining balance (yellow if partial, green if full)
   - Alert box (yellow for partial, green for full)
   - Action buttons

### Failed Submission:
1. Click "Process Payment"
2. Alert popup OR red error box appears
3. Page stays on payment form
4. Error message explains what's wrong
5. Fix the issue and try again

---

## Contact Developer

If none of these solutions work:

1. **Check Browser Console** and copy any error messages
2. **Check Laravel Log** (`storage/logs/laravel.log`) for errors
3. **Check Network Tab** for failed requests
4. **Take screenshots** of:
   - The form with data entered
   - Any error messages
   - Browser console
   - Network tab
5. **Provide this information**:
   - Browser and version
   - Amount entered
   - Payment method selected
   - Booking ID
   - Error message (if any)

---

## Quick Fix Script

If form is completely broken, run this:

```powershell
# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# Refresh autoloader
composer dump-autoload

# Check database
php artisan migrate:status

# Restart server (if using php artisan serve)
# Ctrl+C then:
php artisan serve
```

Then refresh the page and try again.

---

*Last Updated: October 22, 2025*
*For: ValesBeach Resort Payment System*

