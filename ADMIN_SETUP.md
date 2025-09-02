# Admin Setup Instructions

## 1. Update Database Schema

Since we've added the "blocked" status option, you need to refresh your database migrations:

```bash
php artisan migrate:refresh
```

**Warning**: This will delete all existing data. If you have important data, use `php artisan migrate:rollback` and then `php artisan migrate` instead.

## 2. Create an Admin Account

You have two options to create an admin account:

### Option A: Using Laravel Tinker (Recommended)

1. Open your terminal in the project directory
2. Run the following command:

```bash
php artisan tinker
```

3. In the Tinker console, run:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin User',
    'email' => 'admin@valesbeach.com',
    'password' => Hash::make('admin123'),
    'role' => 'admin',
    'status' => 'active'
]);
```

4. Exit Tinker by typing `exit`

### Option B: Using Database Seeder

1. Create a new seeder:

```bash
php artisan make:seeder AdminUserSeeder
```

2. Edit `database/seeders/AdminUserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@valesbeach.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active'
        ]);
    }
}
```

3. Run the seeder:

```bash
php artisan db:seed --class=AdminUserSeeder
```

## 3. Login as Admin

1. Go to your login page: `http://localhost:8000/login`
2. Use these credentials:
   - **Email**: admin@valesbeach.com
   - **Password**: admin123

3. After logging in, you'll be redirected to the admin dashboard

## 4. Test User Management Features

Once logged in as admin, you can:

- **Add new users** with different roles (admin, manager, staff)
- **Edit existing users** including changing their roles
- **Delete users** (except your own account)
- **Activate/Deactivate users** to control access
- **Block users** for security purposes

## 5. Security Notes

- **Change the default admin password** immediately after first login
- Only create admin accounts for trusted personnel
- Regularly review user accounts and their permissions
- Consider using strong passwords for all accounts

## 6. Troubleshooting

If you encounter any issues:

1. Make sure your database is running (XAMPP MySQL)
2. Check that your `.env` file has correct database credentials
3. Ensure migrations have been run successfully
4. Check the browser console for any JavaScript errors
5. Review Laravel logs in `storage/logs/laravel.log`

## Available User Roles

- **Admin**: Full access to all features including user management
- **Manager**: Limited administrative access (to be configured)
- **Staff**: Basic user access (to be configured)

## Available User Statuses

- **Active**: User can log in and use the system
- **Inactive**: User account is temporarily disabled
- **Blocked**: User account is blocked (more restrictive than inactive)
