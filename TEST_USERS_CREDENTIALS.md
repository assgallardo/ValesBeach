# ValesBeach Resort - Test User Credentials

## Test Accounts Created ✅

All test accounts have been successfully created in the database. Use these credentials to log in and test different user roles.

---

## 🔐 Login Credentials

### 1. **Admin Account**
- **Role**: Administrator (Full System Access)
- **Email**: `admin@valesbeach.com`
- **Password**: `admin123`
- **Permissions**: 
  - Full access to all system features
  - User management
  - System settings
  - All reports and analytics
  - Booking management
  - Service requests management

---

### 2. **Manager Account**
- **Role**: Manager
- **Email**: `manager@valesbeach.com`
- **Password**: `manager123`
- **Permissions**: 
  - Manage bookings and reservations
  - View and manage service requests
  - Access reports and dashboards
  - Manage rooms and availability
  - Handle payments and invoices
  - Staff oversight

---

### 3. **Staff Account**
- **Role**: Staff Member
- **Email**: `staff@valesbeach.com`
- **Password**: `staff123`
- **Permissions**: 
  - Process bookings
  - Handle service requests
  - Update booking status
  - View assigned tasks
  - Basic customer service functions

---

### 4. **Guest Account**
- **Role**: Guest/Customer
- **Email**: `guest@valesbeach.com`
- **Password**: `guest123`
- **Permissions**: 
  - Make room bookings
  - Request services
  - View booking history
  - Order food/amenities
  - View invoices and payments
  - Update profile

---

## 🚀 How to Login

1. Navigate to: **http://127.0.0.1:8000**
2. Click on **Login** or **Sign In**
3. Enter the email and password for the role you want to test
4. Click **Login**

---

## 📋 Quick Reference Table

| Role    | Email                    | Password    | Access Level |
|---------|--------------------------|-------------|--------------|
| Admin   | admin@valesbeach.com     | admin123    | Full Access  |
| Manager | manager@valesbeach.com   | manager123  | Management   |
| Staff   | staff@valesbeach.com     | staff123    | Operations   |
| Guest   | guest@valesbeach.com     | guest123    | Customer     |

---

## 🔄 Re-running the Seeder

If you need to recreate these users or add more test data, run:

```bash
php artisan db:seed --class=TestUsersSeeder
```

**Note**: The seeder is configured to NOT delete existing users by default. If you want to start fresh, you can uncomment the `User::truncate()` line in the seeder file.

---

## 🛡️ Security Notes

### ⚠️ Important: Development Only

These are **test accounts with simple passwords** for development purposes only!

**Before deploying to production:**
1. ✅ Delete all test accounts
2. ✅ Use strong, unique passwords
3. ✅ Enable two-factor authentication
4. ✅ Review and restrict user permissions
5. ✅ Implement proper password policies
6. ✅ Add password reset functionality

---

## 📝 Additional Information

### User Model Fields
All users include:
- `name` - User's full name
- `email` - Login email (unique)
- `password` - Hashed password
- `role` - User role (admin, manager, staff, guest)
- `status` - Account status (active, inactive, suspended)
- `email_verified_at` - Email verification timestamp

### Password Hashing
All passwords are securely hashed using Laravel's `Hash::make()` with bcrypt.

### Creating More Users

You can create additional users via:

1. **Laravel Tinker** (Command line):
```bash
php artisan tinker
```
Then:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'New User',
    'email' => 'newuser@example.com',
    'password' => Hash::make('password123'),
    'role' => 'staff',
    'status' => 'active',
    'email_verified_at' => now(),
]);
```

2. **Registration Form** (if enabled on your site)

---

## 🐛 Troubleshooting

### Cannot Login?
- Verify the email and password are correct
- Check that the user status is 'active'
- Clear browser cache and cookies
- Check Laravel logs: `storage/logs/laravel.log`

### "User not found" error?
Re-run the seeder:
```bash
php artisan db:seed --class=TestUsersSeeder
```

### Database Connection Issues?
Verify SQLite database exists:
```bash
php artisan db:show
```

---

**Created**: October 15, 2025  
**Environment**: Development  
**Database**: SQLite

All test users are ready to use! 🎉
