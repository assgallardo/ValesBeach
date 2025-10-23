# 🎯 Quick Reference: Database Sync Across Computers

## ⚡ TL;DR

**On NEW computer:**
```powershell
git clone <repo>
cd ValesBeach
composer install
copy .env.example .env
.\setup-fresh-database.ps1
```

**On EXISTING computer (after git pull):**
```powershell
php artisan migrate
php artisan db:seed
```

---

## 📋 What You Need to Know

### ✅ What's in Git (Tracked)
- ✅ `database/migrations/` - Table structures
- ✅ `database/seeders/` - Sample data scripts  
- ✅ Setup scripts (*.ps1)

### ❌ What's NOT in Git (Ignored)
- ❌ `database/database.sqlite` - Actual database
- ❌ `.env` - Your personal config

---

## 🚀 Commands You'll Use

| Command | When | What It Does |
|---------|------|--------------|
| `php artisan migrate` | After git pull | Adds new tables |
| `php artisan db:seed` | Need fresh data | Reloads sample data |
| `php artisan migrate:fresh --seed` | Complete reset | Deletes everything, starts fresh |
| `.\setup-fresh-database.ps1` | First time | Full automated setup |

---

## 👥 Test Users (Always Available)

After seeding, you get these accounts:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@valesbeach.com | admin123 |
| Manager | manager@valesbeach.com | manager123 |
| Staff | staff@valesbeach.com | staff123 |
| Guest | guest@valesbeach.com | guest123 |

---

## 📊 Sample Data Included

After `db:seed`, you get:
- ✅ 4 test user accounts (all roles)
- ✅ 28 rooms/cottages/halls with prices
- ✅ 20 services and add-ons
- ✅ Complete food menu (5 categories, 20+ items)

---

## 🔄 Common Scenarios

### Scenario 1: New Team Member
```powershell
# They run once:
.\setup-fresh-database.ps1

# Done! They have everything
```

### Scenario 2: You Add a New Feature
```powershell
# You create migration
php artisan make:migration add_something
# Edit the migration file
php artisan migrate

# Commit and push
git add .
git commit -m "Add feature"
git push
```

### Scenario 3: Someone Else Pulls Your Changes
```powershell
# They pull
git pull

# They migrate
php artisan migrate

# Done! They're synced
```

### Scenario 4: Database Got Messed Up
```powershell
# Nuclear option - complete reset
php artisan migrate:fresh --seed

# Back to clean state!
```

---

## ⚠️ Important Rules

### ✅ DO:
- Commit migrations to Git
- Commit seeders to Git
- Run `migrate` after `git pull`
- Use `updateOrCreate` in seeders

### ❌ DON'T:
- Commit `.env` file
- Commit database files
- Edit old migrations
- Forget to run `migrate` after pulling

---

## 🎓 How It Works

1. **Git** stores the code that creates data (not the data itself)
2. **Migrations** = instructions to create tables
3. **Seeders** = instructions to insert sample data
4. Every computer runs the same instructions
5. Result: Identical database everywhere! ✨

---

## 🔧 Troubleshooting

| Problem | Solution |
|---------|----------|
| "Nothing to migrate" | ✓ Good! You're up to date |
| "Table already exists" | Run `migrate:fresh --seed` |
| Different data | Run `db:seed` again |
| Can't login | Check `TEST_USERS_CREDENTIALS.md` |

---

## 📱 Quick Copy-Paste

**Fresh setup (new computer):**
```powershell
.\setup-fresh-database.ps1
```

**Update (existing computer):**
```powershell
git pull ; php artisan migrate ; php artisan db:seed
```

**Reset everything:**
```powershell
php artisan migrate:fresh --seed
```

---

## ✨ The Magic

Everyone gets the **exact same data** because:
- Migrations create identical tables
- Seeders insert identical rows
- All controlled by Git
- No manual database exports needed!

**One source of truth = No confusion! 🎉**
