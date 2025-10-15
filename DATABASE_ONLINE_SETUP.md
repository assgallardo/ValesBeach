# ValesBeach Resort - Online Database Setup Guide

## 🌐 Converting SQLite to Online Database

Your application currently uses SQLite (local file database). To make it accessible online, you need to switch to a client-server database like MySQL or PostgreSQL.

---

## 📊 Database Options

### Option 1: MySQL/MariaDB (Recommended for Laravel)
- Most popular with Laravel
- Excellent documentation and community support
- Easy to find hosting providers

### Option 2: PostgreSQL
- Advanced features
- Better for complex queries
- Great for scalability

### Option 3: Cloud Database Services
- No server management needed
- Auto-scaling and backups
- Pay-as-you-go pricing

---

## 🚀 Quick Setup Options

### A. Free Cloud Database Services

#### 1. **PlanetScale** (MySQL-compatible) - FREE TIER
- **Website**: https://planetscale.com
- **Free Tier**: 5GB storage, 1 billion row reads/month
- **Setup Time**: 5 minutes
- **Best For**: Production-ready MySQL

#### 2. **Neon** (PostgreSQL) - FREE TIER
- **Website**: https://neon.tech
- **Free Tier**: 512 MB storage, 3GB data transfer
- **Setup Time**: 5 minutes
- **Best For**: Modern PostgreSQL features

#### 3. **Railway** - FREE TIER
- **Website**: https://railway.app
- **Free Tier**: $5 credit/month
- **Supports**: MySQL, PostgreSQL
- **Setup Time**: 10 minutes

#### 4. **Supabase** (PostgreSQL) - FREE TIER
- **Website**: https://supabase.com
- **Free Tier**: 500 MB database, unlimited API requests
- **Setup Time**: 5 minutes
- **Bonus**: Built-in authentication & real-time features

---

### B. Local MySQL Server (For Development/Testing)

#### Install MySQL on Windows

**Using winget (Easiest):**
```powershell
# Install MySQL Server
winget install Oracle.MySQL

# Or install MariaDB (MySQL fork)
winget install MariaDB.Server
```

**After Installation:**
1. MySQL runs on `localhost:3306`
2. Default username: `root`
3. Password: Set during installation
4. Access MySQL: `mysql -u root -p`

---

## 📝 Step-by-Step: Switch from SQLite to MySQL

### Step 1: Update `.env` File

Open `C:\Users\sethy\ValesBeach\.env` and change database settings:

**FROM (SQLite):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=C:\Users\sethy\ValesBeach\database\database.sqlite
```

**TO (MySQL Local):**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=valesbeach
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

**OR (Cloud Database - Example with PlanetScale):**
```env
DB_CONNECTION=mysql
DB_HOST=aws.connect.psdb.cloud
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
MYSQL_ATTR_SSL_CA=/etc/ssl/cert.pem
```

---

### Step 2: Install MySQL PHP Extension

Check if MySQL extension is enabled:

```powershell
php -m | Select-String -Pattern "mysql"
```

If not found, enable it in `php.ini`:

**Location:** `C:\Users\sethy\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.ini`

**Uncomment these lines (remove `;`):**
```ini
extension=pdo_mysql
extension=mysqli
```

**Verify:**
```powershell
php -m | Select-String -Pattern "mysql"
```

---

### Step 3: Create Database

#### For Local MySQL:
```sql
mysql -u root -p
CREATE DATABASE valesbeach CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### For Cloud Services:
- Database is usually auto-created through their dashboard
- Just copy the connection details they provide

---

### Step 4: Run Migrations

```powershell
cd C:\Users\sethy\ValesBeach

# Clear config cache
php artisan config:clear

# Test database connection
php artisan db:show

# Run migrations
php artisan migrate

# Seed test users
php artisan db:seed --class=TestUsersSeeder
```

---

### Step 5: Migrate Existing Data (Optional)

If you want to keep your SQLite data, export and import it:

**Export from SQLite:**
```powershell
# Export to SQL file
php artisan db:show --database=sqlite
# Use Laravel package or manual export
```

**Better Option - Use a Migration Tool:**
```powershell
# Install SQLite to MySQL converter
composer require doctrine/dbal --dev

# Then use Laravel's database seeding to copy data
```

---

## 🔧 Automated Setup Script

### Quick MySQL Setup (Local)

Save this as `setup-mysql.ps1`:

```powershell
# ValesBeach MySQL Setup Script

Write-Host "🚀 ValesBeach MySQL Database Setup" -ForegroundColor Cyan
Write-Host "======================================`n" -ForegroundColor Cyan

# Check if MySQL is installed
$mysqlInstalled = Get-Command mysql -ErrorAction SilentlyContinue

if (!$mysqlInstalled) {
    Write-Host "⚠ MySQL not found. Installing..." -ForegroundColor Yellow
    winget install Oracle.MySQL
    Write-Host "✅ MySQL installed. Please set root password during setup.`n" -ForegroundColor Green
    Write-Host "⏸ Restart this script after MySQL installation is complete." -ForegroundColor Yellow
    exit
}

# Prompt for MySQL credentials
$mysqlUser = Read-Host "Enter MySQL username (default: root)"
if ([string]::IsNullOrWhiteSpace($mysqlUser)) { $mysqlUser = "root" }

$mysqlPass = Read-Host "Enter MySQL password" -AsSecureString
$mysqlPassPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [Runtime.InteropServices.Marshal]::SecureStringToBSTR($mysqlPass)
)

# Create database
Write-Host "`n📊 Creating database 'valesbeach'..." -ForegroundColor Yellow
$createDbCmd = "CREATE DATABASE IF NOT EXISTS valesbeach CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u $mysqlUser -p$mysqlPassPlain -e $createDbCmd

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Database created successfully!`n" -ForegroundColor Green
} else {
    Write-Host "❌ Failed to create database. Check credentials.`n" -ForegroundColor Red
    exit
}

# Update .env file
Write-Host "📝 Updating .env file..." -ForegroundColor Yellow
$envPath = "C:\Users\sethy\ValesBeach\.env"
$envContent = Get-Content $envPath

$newEnvContent = $envContent -replace 'DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql'
$newEnvContent = $newEnvContent -replace 'DB_DATABASE=.*database\.sqlite', "DB_DATABASE=valesbeach"
$newEnvContent = $newEnvContent | ForEach-Object {
    if ($_ -match '^DB_CONNECTION=') {
        $_
        "DB_HOST=127.0.0.1"
        "DB_PORT=3306"
        "DB_DATABASE=valesbeach"
        "DB_USERNAME=$mysqlUser"
        "DB_PASSWORD=$mysqlPassPlain"
    } elseif ($_ -notmatch '^DB_(HOST|PORT|DATABASE|USERNAME|PASSWORD)=') {
        $_
    }
}

Set-Content $envPath -Value $newEnvContent
Write-Host "✅ .env file updated`n" -ForegroundColor Green

# Clear config cache
Write-Host "🔄 Clearing Laravel cache..." -ForegroundColor Yellow
cd C:\Users\sethy\ValesBeach
php artisan config:clear
php artisan cache:clear

# Test connection
Write-Host "`n🔌 Testing database connection..." -ForegroundColor Yellow
php artisan db:show

# Run migrations
Write-Host "`n📦 Running migrations..." -ForegroundColor Yellow
php artisan migrate

# Seed users
Write-Host "`n👥 Creating test users..." -ForegroundColor Yellow
php artisan db:seed --class=TestUsersSeeder

Write-Host "`n✅ MySQL Database Setup Complete!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "📊 Database: valesbeach" -ForegroundColor White
Write-Host "🔌 Connection: mysql" -ForegroundColor White
Write-Host "🌐 Host: 127.0.0.1:3306" -ForegroundColor White
Write-Host "======================================`n" -ForegroundColor Cyan
```

**Run it:**
```powershell
cd C:\Users\sethy\ValesBeach
.\setup-mysql.ps1
```

---

## 🌐 Cloud Database Setup (Example: PlanetScale)

### Step-by-Step with PlanetScale (FREE)

#### 1. **Sign Up**
- Go to https://planetscale.com
- Sign up with GitHub (easiest)
- Verify your email

#### 2. **Create Database**
- Click "New Database"
- Name: `valesbeach`
- Region: Choose closest to you
- Click "Create database"

#### 3. **Get Connection String**
- Click on your database
- Go to "Connect" tab
- Select "Laravel" from framework dropdown
- Copy the connection details

#### 4. **Update `.env`**
Replace database section with provided credentials:
```env
DB_CONNECTION=mysql
DB_HOST=aws.connect.psdb.cloud
DB_PORT=3306
DB_DATABASE=valesbeach
DB_USERNAME=xxxxxxxxxxxx
DB_PASSWORD=pscale_pw_xxxxxxxxxxxx
MYSQL_ATTR_SSL_CA=/etc/ssl/cert.pem
```

#### 5. **Deploy**
```powershell
php artisan config:clear
php artisan migrate
php artisan db:seed --class=TestUsersSeeder
```

✅ **Done! Your database is now online and accessible from anywhere!**

---

## 🔐 Security Best Practices

### 1. **Secure Credentials**
```env
# Never commit .env to Git!
# Add to .gitignore (already done in Laravel)
```

### 2. **Use Environment Variables in Production**
- Don't hardcode passwords
- Use server environment variables
- Rotate passwords regularly

### 3. **SSL/TLS Connections**
```env
# For production databases
DB_SSL_MODE=required
MYSQL_ATTR_SSL_CA=/path/to/ca-cert.pem
```

### 4. **Limit Database User Permissions**
```sql
-- Create dedicated user (not root)
CREATE USER 'valesbeach_user'@'%' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON valesbeach.* TO 'valesbeach_user'@'%';
FLUSH PRIVILEGES;
```

---

## 🚀 Deploy to Production

### Recommended Hosting Providers

#### 1. **Laravel Forge** + Any VPS
- **Cost**: $12/month + VPS ($5-20/month)
- **Best For**: Professional Laravel hosting
- **Includes**: Auto-deployment, SSL, database management

#### 2. **Laravel Vapor** (AWS Serverless)
- **Cost**: $39/month + AWS usage
- **Best For**: Scalable applications
- **Includes**: Auto-scaling, CDN, database

#### 3. **DigitalOcean App Platform**
- **Cost**: $5-12/month
- **Best For**: Simple deployment
- **Includes**: Database, auto-deploy from Git

#### 4. **Railway**
- **Cost**: $5/month free credit
- **Best For**: Quick deployment
- **Includes**: Database, auto-deploy

---

## 📊 Comparison Table

| Service | Database | Free Tier | Best For |
|---------|----------|-----------|----------|
| PlanetScale | MySQL | ✅ 5GB | Production MySQL |
| Neon | PostgreSQL | ✅ 512MB | Modern PostgreSQL |
| Supabase | PostgreSQL | ✅ 500MB | Real-time features |
| Railway | Both | ✅ $5 credit | Full-stack deploy |
| Local MySQL | MySQL | ✅ Unlimited | Development |

---

## 🆘 Troubleshooting

### "SQLSTATE[HY000] [2002] Connection refused"
- Check MySQL is running: `Get-Service MySQL* | Format-Table`
- Verify credentials in `.env`
- Check firewall settings

### "Access denied for user"
- Verify username and password
- Check user has privileges: `SHOW GRANTS FOR 'user'@'host';`
- Reset password if needed

### "Unknown database 'valesbeach'"
- Create database: `CREATE DATABASE valesbeach;`
- Check database name in `.env` matches

### SSL/TLS Issues with Cloud Databases
```env
# Try disabling SSL verification (development only!)
DB_SSL_MODE=disabled
```

---

## 📚 Next Steps

1. ✅ Choose your database option (local or cloud)
2. ✅ Install/setup database server
3. ✅ Update `.env` configuration
4. ✅ Enable MySQL PHP extensions
5. ✅ Run migrations and seeders
6. ✅ Test the connection
7. ✅ Deploy to production (optional)

---

## 🤝 Need Help?

- **Laravel Database Docs**: https://laravel.com/docs/11.x/database
- **PlanetScale Docs**: https://planetscale.com/docs
- **Railway Docs**: https://docs.railway.app
- **MySQL Tutorial**: https://dev.mysql.com/doc/

---

**Created**: October 15, 2025  
**Current DB**: SQLite (Local)  
**Recommended**: PlanetScale or Local MySQL for development

Your application is ready to scale! 🚀
