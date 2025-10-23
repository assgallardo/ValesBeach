# Setup ValesBeach on a New Computer

## 🚀 Quick Setup (5 minutes)

### Step 1: Clone & Install
```powershell
# Clone the repository
git clone https://github.com/assgallardo/ValesBeach.git
cd ValesBeach

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### Step 2: Environment Setup
```powershell
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### Step 3: Configure Database
Edit `.env` file:

**For SQLite (Easiest):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=C:\Users\sethy\ValesBeach\database\database.sqlite
```

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=valesbeach
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 4: Setup Database
```powershell
# Run the automated setup script
.\setup-fresh-database.ps1
```

**OR manually:**
```powershell
php artisan migrate
php artisan db:seed
```

### Step 5: Start Development
```powershell
# Start Laravel server
php artisan serve

# In another terminal, start Vite (for frontend)
npm run dev
```

---

## ✅ What Gets Created

The database will be automatically populated with:

### 👥 Users (4 test accounts)
- **Guest:** guest@test.com / password
- **Admin:** admin@test.com / password  
- **Manager:** manager@test.com / password
- **Staff:** staff@test.com / password

### 🏠 Rooms & Cottages
- Standard Room
- Deluxe Room
- Suite
- Beach Cottage
- Family Cottage
- With rates and availability

### 🛎️ Services
- Laundry Service
- Room Service
- Tour Packages
- Massage/Spa
- Equipment Rental
- With pricing

### 🍽️ Food Menu
- **Categories:** Breakfast, Lunch, Dinner, Drinks, Desserts
- **Items:** Multiple menu items per category
- With prices and descriptions

---

## 🔄 Updating on Existing Computer

When you pull new changes:

```powershell
# Pull latest code
git pull

# Update dependencies
composer install
npm install

# Run any new migrations
php artisan migrate

# If new seeders were added, re-seed
php artisan db:seed
```

---

## 🔧 Useful Commands

### Database Management
```powershell
# Check migration status
php artisan migrate:status

# View database info
php artisan db:show

# View specific table
php artisan db:table users --show

# Fresh start (reset everything)
php artisan migrate:fresh --seed
```

### Development
```powershell
# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test

# Check routes
php artisan route:list
```

---

## 📁 Important Files

| File | Purpose |
|------|---------|
| `.env` | Database & app configuration |
| `database/migrations/` | Database structure (version controlled) |
| `database/seeders/` | Sample data (version controlled) |
| `TEST_USERS_CREDENTIALS.md` | Login credentials |

---

## ⚠️ Troubleshooting

### "Could not find driver"
Install PHP extensions:
```powershell
# For SQLite
# Enable extension=pdo_sqlite in php.ini

# For MySQL
# Enable extension=pdo_mysql in php.ini
```

### "Database does not exist"
For SQLite:
```powershell
# Create empty file
New-Item database\database.sqlite -ItemType File
```

For MySQL:
```powershell
# Create database in MySQL
mysql -u root -p
CREATE DATABASE valesbeach;
exit
```

### "Permission denied" on database file
```powershell
# Make sure storage folder is writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## 🎯 Why This Works

- **Migrations:** Define database structure (version controlled)
- **Seeders:** Populate with consistent data (version controlled)
- **No SQL files needed:** Everything in PHP code
- **Git-friendly:** All tracked in version control
- **Consistent:** Same data on every computer

When you `git pull` + `migrate` + `seed`, you get identical data every time!

---

## 🤝 Team Workflow

### Developer A (You):
```powershell
# Make changes, create migration
php artisan make:migration add_something
# Edit migration file
php artisan migrate
git add .
git commit -m "Add new feature"
git push
```

### Developer B (Teammate):
```powershell
# Pull changes
git pull

# Run new migrations
php artisan migrate

# If new seeders added
php artisan db:seed
```

Everyone stays in sync! 🎉
