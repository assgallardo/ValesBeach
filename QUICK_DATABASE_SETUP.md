# ValesBeach - Quick Database Setup Commands

## 🚀 EASIEST WAY: Copy & Paste Commands

### Option 1: Run the Script (Bypass Execution Policy)

Just copy and paste this single command:

```powershell
powershell -ExecutionPolicy Bypass -File .\migrate-database.ps1
```

This runs the script once without changing your system settings.

---

### Option 2: Enable Scripts for Your User (Recommended)

Run this command to enable scripts permanently for your user account:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

Then run:
```powershell
.\migrate-database.ps1
```

---

### Option 3: Manual Quick Setup (Cloud Database)

**If you have PlanetScale credentials, run these commands one by one:**

```powershell
# 1. Backup your current .env
Copy-Item .env ".env.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"

# 2. Update .env with your credentials (edit the values in quotes)
(Get-Content .env) -replace 'DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql' | Set-Content .env
Add-Content .env "`nDB_HOST=your_host_here"
Add-Content .env "DB_PORT=3306"
Add-Content .env "DB_DATABASE=your_database_name"
Add-Content .env "DB_USERNAME=your_username"
Add-Content .env "DB_PASSWORD=your_password"

# 3. Clear cache and test
php artisan config:clear
php artisan db:show

# 4. Run migrations
php artisan migrate

# 5. Create test users
php artisan db:seed --class=TestUsersSeeder
```

---

### Option 4: Edit .env File Manually

1. **Open .env file:**
   ```powershell
   notepad .env
   ```

2. **Find the database section and replace it with:**

   **For PlanetScale (Cloud):**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=aws.connect.psdb.cloud
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=pscale_pw_xxxxxxxxxxxxx
   MYSQL_ATTR_SSL_CA=/etc/ssl/cert.pem
   ```

   **For Local MySQL:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=valesbeach
   DB_USERNAME=root
   DB_PASSWORD=your_mysql_password
   ```

3. **Save the file (Ctrl+S) and close Notepad**

4. **Run these commands:**
   ```powershell
   php artisan config:clear
   php artisan db:show
   php artisan migrate
   php artisan db:seed --class=TestUsersSeeder
   ```

---

## 🌐 Get PlanetScale Credentials (5 Minutes)

1. Go to: **https://planetscale.com**
2. Sign up with GitHub (fastest)
3. Click "Create Database"
4. Name it: **valesbeach**
5. Choose your region
6. Click "Create database"
7. Click "Connect" button
8. Select "Laravel" from dropdown
9. Copy the credentials shown
10. Use them in your .env file

**Free Forever:**
- 5 GB storage
- 1 billion row reads/month
- No credit card required

---

## 💻 Install MySQL Locally (Alternative)

```powershell
# Install MySQL
winget install Oracle.MySQL

# During installation, set a root password and remember it!
# After installation, restart your terminal and run the setup again
```

---

## ✅ Verify Setup

After configuration, run:

```powershell
php artisan db:show
```

You should see your MySQL database info instead of SQLite!

---

## 🆘 Troubleshooting

### "Access denied for user"
- Check username and password in .env
- Verify database exists
- For local MySQL, ensure MySQL service is running

### "could not find driver"
- MySQL extensions should already be enabled
- Verify with: `php -m | Select-String mysql`
- Should show: pdo_mysql and mysqli

### "Connection refused"
- Check database host is correct
- Verify port number (usually 3306)
- For local MySQL: ensure MySQL is running
- For cloud: check firewall/VPN settings

---

## 📱 Contact Support

- **Laravel Database Docs**: https://laravel.com/docs/database
- **PlanetScale Docs**: https://planetscale.com/docs
- **MySQL Docs**: https://dev.mysql.com/doc/

---

**Quick Start Recommendation:**

1. Use **PlanetScale** (cloud) for easiest setup
2. Run: `powershell -ExecutionPolicy Bypass -File .\migrate-database.ps1`
3. Choose option 1 (PlanetScale)
4. Follow the prompts

Done! 🎉
