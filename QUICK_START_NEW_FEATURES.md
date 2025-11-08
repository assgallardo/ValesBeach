# QUICK START GUIDE - NEW FEATURES
**Vales Beach Resort Management System**

---

## 🚀 QUICK SETUP (5 Minutes)

### Step 1: Run Migration
```bash
php artisan migrate
```
This creates the `housekeeping_requests` table.

### Step 2: Clear Cache (Optional)
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 3: Test the Features
You're ready to go! The features use existing data.

---

## 📊 CUSTOMER REPORTS - QUICK ACCESS

### Location
**Manager/Admin Dashboard** → **Reports & Analytics** → **Customer Analytics Section**

### Three New Reports:

1. **Repeat Customers** 🔄
   - Shows customers with 2+ bookings
   - Displays retention rate
   - URL: `/manager/reports/repeat-customers`

2. **Customer Preferences** ❤️
   - Room type preferences
   - Service preferences
   - Food preferences
   - Peak booking days
   - URL: `/manager/reports/customer-preferences`

3. **Payment Methods** 💳
   - Payment method breakdown
   - Transaction statistics
   - Payment trends
   - URL: `/manager/reports/payment-methods`

### How to Use:
1. Click on any report card
2. Use date range filters if needed
3. View insights and statistics
4. Data updates automatically based on filters

---

## 🧹 HOUSEKEEPING - QUICK ACCESS

### Location
**Manager/Admin Dashboard** → **Housekeeping Management** (New Menu Item)

### How It Works:

**Automatic Trigger:**
- When you mark a booking as "Checked Out"
- System automatically creates housekeeping request
- Request appears in Housekeeping Management
- Staff can see it immediately

**Manual Management:**
1. Go to Housekeeping Management
2. View all pending requests
3. Assign to staff member (click "Assign" button)
4. Staff updates status as they work
5. Mark complete when done

### Status Flow:
```
Pending → Assigned → In Progress → Completed
```

### Quick Actions:
- **Assign** - Select staff member from dropdown
- **Update Status** - Change request status + add notes
- **Filter** - By status, priority, or date range

---

## 💡 COMMON TASKS

### Task 1: View Repeat Customers
1. Go to Reports & Analytics
2. Click "Repeat Customers" card
3. See list of loyal customers
4. Note their total spent and booking count

### Task 2: Analyze Payment Methods
1. Go to Reports & Analytics
2. Click "Payment Methods" card
3. View most popular payment method
4. Check breakdown by source (bookings, food, services)

### Task 3: Assign Housekeeping After Checkout
1. In Bookings, mark guest as "Checked Out"
2. Go to Housekeeping Management
3. See auto-created request
4. Click "Assign" button
5. Select staff member
6. Staff can now see it in their view

### Task 4: Complete Housekeeping Task (Staff)
1. Go to Housekeeping Management
2. Find your assigned request
3. Click "Update" button
4. Change status to "In Progress"
5. When done, update to "Completed"
6. Add completion notes (optional)

---

## 🎯 KEY FEATURES AT A GLANCE

### Customer Reports:
✅ Date range filtering  
✅ Real-time statistics  
✅ Visual breakdown  
✅ Pagination for large datasets  
✅ Export-ready data  

### Housekeeping:
✅ Automatic creation on checkout  
✅ Staff assignment  
✅ Priority levels (low, normal, high, urgent)  
✅ Status tracking  
✅ Notes and timestamps  
✅ Filter by status/priority/date  

---

## 🔧 TROUBLESHOOTING

**Q: I don't see the new reports**
- A: Clear cache: `php artisan config:clear && php artisan route:clear`
- Check you're logged in as Manager or Admin

**Q: Housekeeping request not created on checkout**
- A: Make sure you ran `php artisan migrate`
- Check booking status is actually "checked_out"
- Look in Laravel logs: `storage/logs/laravel.log`

**Q: No data in customer reports**
- A: Adjust date range to include more days
- Ensure you have bookings and payments in the database
- Payments must be marked as "completed" to show in reports

**Q: Cannot assign staff to housekeeping**
- A: Verify user has "staff" role
- Check staff member exists in users table
- Ensure request is not already completed

---

## 📱 NAVIGATION

### Manager/Admin Menu:
```
Dashboard
├── Reports & Analytics (Updated)
│   ├── [Existing Reports]
│   └── Customer Analytics (NEW)
│       ├── Repeat Customers
│       ├── Customer Preferences
│       └── Payment Methods
│
└── Housekeeping Management (NEW)
    ├── Dashboard
    ├── Pending Requests
    ├── Assigned Requests
    ├── In Progress
    └── Completed
```

---

## 📞 NEED HELP?

1. Check full documentation: `NEW_FEATURES_IMPLEMENTATION.md`
2. Review Laravel logs: `storage/logs/laravel.log`
3. Run diagnostics: `php artisan migrate:status`

---

## ✅ VERIFICATION CHECKLIST

After setup, verify these work:

**Customer Reports:**
- [ ] Can access Repeat Customers report
- [ ] Can access Customer Preferences report
- [ ] Can access Payment Methods report
- [ ] Date filtering works
- [ ] Statistics display correctly

**Housekeeping:**
- [ ] Can access Housekeeping Management
- [ ] Auto-creation works on checkout
- [ ] Can assign staff to request
- [ ] Can update status
- [ ] Can filter requests
- [ ] Statistics display correctly

---

## 🎉 YOU'RE ALL SET!

The new features are ready to use. Start by:
1. Creating a test checkout to trigger housekeeping
2. Exploring the customer reports
3. Assigning housekeeping tasks to staff

**Enjoy the enhanced functionality!**

---

*Quick Start Guide v1.0*  
*Last Updated: November 8, 2025*
