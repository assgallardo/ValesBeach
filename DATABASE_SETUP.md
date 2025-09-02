# Database Setup Instructions for Vales Beach Resort

## Prerequisites
Make sure you have XAMPP running with Apache and MySQL services started.

## Step 1: Create Database
1. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
2. Click "New" to create a new database
3. Name it `valesbeach`
4. Set collation to `utf8mb4_unicode_ci`
5. Click "Create"

## Step 2: Configure Environment Variables
The database environment variables have already been set via the DevServerControl tool:
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=valesbeach
- DB_USERNAME=root
- DB_PASSWORD= (empty for XAMPP default)

## Step 3: Run Database Migrations
Open your terminal/command prompt in the project directory and run:

```bash
php artisan migrate
```

This will create the following tables:
- `users` (with name, email, password, role, status fields)
- `password_reset_tokens`
- `sessions`
- `cache`
- `jobs`

## Step 4: Create Admin User (Optional)
To create an admin user for testing, you can run:

```bash
php artisan tinker
```

Then in the tinker shell:
```php
User::create([
    'name' => 'Admin User',
    'email' => 'admin@valesbeach.com',
    'password' => Hash::make('password123'),
    'role' => 'admin',
    'status' => 'active'
]);
```

Type `exit` to leave tinker.

## Step 5: Test the Application
1. Make sure both servers are running:
   - `npm run dev` (for assets)
   - `php artisan serve` (for Laravel)

2. Visit the following URLs to test:
   - **Home**: http://localhost:8000/
   - **Login**: http://localhost:8000/login
   - **Signup**: http://localhost:8000/signup
   - **Admin Dashboard**: http://localhost:8000/admin (requires login)
   - **User Management**: http://localhost:8000/admin/users (requires login)

## Features Available:
✅ **User Registration**: Full signup with validation
✅ **User Login**: Email/password authentication with remember me
✅ **User Management**: CRUD operations (Create, Read, Update, Delete)
✅ **Role Management**: Admin, Manager, Staff roles
✅ **Status Management**: Active/Inactive user status
✅ **Search Users**: Search functionality in user management
✅ **Responsive Design**: Works on mobile, tablet, and desktop

## Database Structure:
- **users table**:
  - id (primary key)
  - name (string)
  - email (unique string)
  - password (hashed)
  - role (enum: admin, manager, staff)
  - status (enum: active, inactive)
  - email_verified_at (timestamp)
  - remember_token
  - created_at, updated_at

## Test Credentials:
If you created the admin user above:
- **Email**: admin@valesbeach.com
- **Password**: password123

## Troubleshooting:
1. **Migration Error**: Make sure XAMPP MySQL is running and the database `valesbeach` exists
2. **Connection Error**: Check that DB credentials match your XAMPP setup
3. **Permission Error**: Make sure your project has proper file permissions
4. **Asset Error**: Run `npm run build` if styles aren't loading properly

## Security Note:
In production, make sure to:
- Use strong passwords
- Set proper environment variables
- Enable HTTPS
- Configure proper database permissions
