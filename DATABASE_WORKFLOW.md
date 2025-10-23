# 🎯 Database Consistency Workflow

## How It Works

Your ValesBeach project now uses **migrations + seeders** to ensure every computer has identical data.

---

## 📦 What's Tracked in Git

✅ **Tracked (committed to Git):**
- `database/migrations/` - Database structure
- `database/seeders/` - Sample data scripts
- `database/factories/` - Data generators
- All setup scripts

❌ **Not Tracked (ignored by Git):**
- `database/database.sqlite` - Actual database file
- `.env` - Environment configuration
- Any database exports

---

## 🚀 Setup on New Computer

### First Time Setup:
```powershell
# 1. Clone repository
git clone https://github.com/assgallardo/ValesBeach.git
cd ValesBeach

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
copy .env.example .env
php artisan key:generate

# 4. Configure database in .env (use SQLite for simplicity)

# 5. Run automated setup
.\setup-fresh-database.ps1
```

**That's it!** You now have:
- ✅ Database structure (from migrations)
- ✅ Test users (guest, admin, manager, staff)
- ✅ Rooms and cottages
- ✅ Services
- ✅ Food menu
- ✅ All the same data as every other computer

---

## 🔄 Staying in Sync

### When Someone Pushes Changes:

**Developer A (Makes changes):**
```powershell
# 1. Create new migration if needed
php artisan make:migration add_new_feature

# 2. Edit migration file in database/migrations/

# 3. Test it
php artisan migrate

# 4. Update seeder if needed
# Edit database/seeders/SomeSeeder.php

# 5. Commit and push
git add .
git commit -m "Add new feature"
git push
```

**Developer B (Receives changes):**
```powershell
# 1. Pull latest code
git pull

# 2. Run new migrations
php artisan migrate

# 3. Re-seed if seeders changed (optional)
php artisan db:seed

# Done! You're in sync
```

---

## ⚡ Quick Commands

### Fresh Database Setup
```powershell
# Automated (recommended)
.\setup-fresh-database.ps1

# Manual
php artisan migrate:fresh --seed
```

### Update Existing Database
```powershell
# Run only new migrations
php artisan migrate

# Re-run all seeders
php artisan db:seed

# Or both together (safe - doesn't delete data)
php artisan migrate
php artisan db:seed
```

### Check Status
```powershell
# See which migrations ran
php artisan migrate:status

# View database structure
php artisan db:show

# List all tables
php artisan db:table users --show
```

---

## 📊 What Gets Seeded

Every time you run `php artisan db:seed`, you get:

### 👥 Test Users
```
guest@test.com / password (Guest role)
admin@test.com / password (Admin role)
manager@test.com / password (Manager role)
staff@test.com / password (Staff role)
```

### 🏠 Rooms & Rates
- 7+ different room types
- With prices and availability
- Room features and descriptions

### 🛎️ Services
- Laundry, Room Service, Tours
- Massage/Spa, Equipment Rental
- With pricing

### 🍽️ Food Menu
- 5 categories (Breakfast, Lunch, Dinner, etc.)
- 20+ menu items
- Prices and descriptions

---

## 🎯 Key Benefits

| Benefit | Description |
|---------|-------------|
| **Consistent** | Same data on every computer |
| **Version Controlled** | All changes tracked in Git |
| **No Large Files** | No SQL dumps in Git |
| **Easy Updates** | Just `git pull` + `migrate` |
| **Testable** | Fresh data anytime |
| **Documented** | Migrations show history |

---

## 🔧 Advanced Usage

### Reset Everything
```powershell
# Nuclear option - deletes all data and starts fresh
php artisan migrate:fresh --seed
```

### Add New Sample Data
```powershell
# Create new seeder
php artisan make:seeder NewDataSeeder

# Edit database/seeders/NewDataSeeder.php

# Add to DatabaseSeeder.php
$this->call([NewDataSeeder::class]);

# Run it
php artisan db:seed --class=NewDataSeeder
```

### Run Specific Seeder
```powershell
# Run just one seeder
php artisan db:seed --class=MenuItemSeeder
```

---

## 📝 Best Practices

### ✅ DO:
- Commit migrations and seeders to Git
- Use descriptive migration names
- Keep seeders idempotent (can run multiple times)
- Test migrations before pushing
- Document breaking changes

### ❌ DON'T:
- Commit `.env` file
- Commit database files (`.sqlite`, etc.)
- Edit old migrations after pushing
- Delete migrations from Git
- Store production data in seeders

---

## 🚨 Troubleshooting

### "Nothing to migrate"
✓ Good! Means you're up to date.

### "Table already exists"
```powershell
# Reset and start fresh
php artisan migrate:fresh --seed
```

### Seeders not running
```powershell
# Check DatabaseSeeder.php has the call() array
# Run with verbose output
php artisan db:seed --verbose
```

### Different data on different computers
```powershell
# Make sure seeders are committed
git status

# Pull latest seeders
git pull

# Re-seed
php artisan db:seed --class=DatabaseSeeder
```

---

## 📚 Files Reference

| File | Purpose | Git? |
|------|---------|------|
| `database/migrations/*.php` | Table structures | ✅ Yes |
| `database/seeders/*.php` | Sample data | ✅ Yes |
| `database/database.sqlite` | Actual data | ❌ No |
| `.env` | Configuration | ❌ No |
| `setup-fresh-database.ps1` | Setup script | ✅ Yes |

---

## 🎓 How It Works Internally

1. **Migrations** create tables based on PHP code
2. **Seeders** insert rows based on PHP code
3. **Git** tracks both migrations and seeders
4. When you `git pull`, you get the code
5. When you `migrate`, tables are created
6. When you `seed`, data is inserted
7. Result: Identical database on every machine!

---

## 🤝 Team Benefits

- ✅ New team member? `setup-fresh-database.ps1` - Done in 30 seconds
- ✅ Updated schema? `git pull` + `php artisan migrate` - Synced
- ✅ Need fresh data? `php artisan db:seed` - Reset anytime
- ✅ Database issues? `migrate:fresh --seed` - Clean slate
- ✅ No confusion about versions or missing data

---

## 🎉 Summary

**One command to rule them all:**
```powershell
.\setup-fresh-database.ps1
```

This gives everyone the **exact same database** every time! 🚀
