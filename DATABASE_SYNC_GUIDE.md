# Database Synchronization Guide

## Overview
This guide explains how to keep your ValesBeach database consistent across different computers.

## Method 1: Migrations + Seeders (Recommended for Development)

### On Any Computer:
```powershell
# 1. Pull latest code
git pull

# 2. Copy .env.example to .env if needed
copy .env.example .env

# 3. Configure your database in .env
# Edit DB_CONNECTION, DB_DATABASE, etc.

# 4. Run migrations to create tables
php artisan migrate

# 5. Run seeders to populate with test data
php artisan db:seed
```

### Advantages:
- ✓ Version controlled
- ✓ Consistent structure
- ✓ No large files
- ✓ Easy to track changes

### Best For:
- Development environments
- New team members
- Clean database setup

---

## Method 2: Export/Import SQL (For Production Data)

### Export Database (Computer A):
```powershell
# Export your current database
.\export-database.ps1

# This creates: database/exports/valesbeach_YYYY-MM-DD_HHMMSS.sql
```

### Share the Export:
**Option A: Git (Small databases)**
```powershell
# Add to .gitignore exception
git add database/exports/valesbeach_*.sql
git commit -m "Add database export"
git push
```

**Option B: Cloud Storage (Larger databases)**
- Upload to Google Drive, Dropbox, etc.
- Share link with team

**Option C: Direct Transfer**
- Copy file via USB, network share, etc.

### Import Database (Computer B):
```powershell
# 1. Pull code (if using Git)
git pull

# 2. Place SQL file in database/exports/

# 3. Import database
.\import-database.ps1

# Follow prompts to select file
```

### Advantages:
- ✓ Preserves actual data
- ✓ Exact copy of production
- ✓ Includes all records

### Best For:
- Production data sync
- Testing with real data
- Backup/restore

---

## Method 3: Remote Database (Team Development)

### Setup Shared Database:
1. Host MySQL on a server (AWS, DigitalOcean, etc.)
2. Configure `.env` on all computers:

```env
DB_CONNECTION=mysql
DB_HOST=your-server-ip
DB_PORT=3306
DB_DATABASE=valesbeach
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Advantages:
- ✓ Always in sync
- ✓ No manual export/import
- ✓ Real-time collaboration

### Disadvantages:
- ✗ Requires internet
- ✗ Slower than local
- ✗ Costs money

### Best For:
- Team collaboration
- Remote work
- Production environments

---

## Quick Reference

### Fresh Start on New Computer:
```powershell
# 1. Clone repository
git clone <repo-url>
cd ValesBeach

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
copy .env.example .env
php artisan key:generate

# 4. Setup database
php artisan migrate
php artisan db:seed

# 5. Start server
php artisan serve
```

### Sync Existing Database:
```powershell
# On Computer A (export)
.\export-database.ps1

# Transfer file to Computer B

# On Computer B (import)
.\import-database.ps1
```

### Check Database Status:
```powershell
# See migration status
php artisan migrate:status

# See database info
php artisan db:show

# See tables
php artisan db:table users --show
```

---

## File Locations

| Purpose | Location |
|---------|----------|
| Migrations | `database/migrations/` |
| Seeders | `database/seeders/` |
| SQLite DB | `database/database.sqlite` |
| Export Script | `export-database.ps1` |
| Import Script | `import-database.ps1` |
| Exports | `database/exports/` |

---

## Tips

### For SQLite:
- Just copy `database/database.sqlite` file
- Commit to Git if small enough
- Very portable and simple

### For MySQL:
- Use migrations for structure
- Use export/import for data
- Consider remote database for teams

### For Security:
- Never commit `.env` file
- Don't commit production databases
- Use separate databases for dev/production

---

## Troubleshooting

### "Table already exists" error:
```powershell
# Reset and re-migrate
php artisan migrate:fresh
php artisan db:seed
```

### Export script not working:
- Check if mysqldump is installed: `mysqldump --version`
- For SQLite, just copy the .sqlite file manually

### Import fails:
- Make sure database exists
- Check credentials in .env
- Verify SQL file isn't corrupted

---

## Which Method Should I Use?

| Scenario | Recommended Method |
|----------|-------------------|
| New developer joining | Method 1 (Migrations + Seeders) |
| Need exact production data | Method 2 (Export/Import) |
| Team working together | Method 3 (Remote Database) |
| Personal laptop + desktop | Method 2 (Export/Import) |
| Development + testing | Method 1 (Migrations + Seeders) |
| Backup before changes | Method 2 (Export/Import) |

---

## Automation Ideas

### Auto-export on changes:
Add to your workflow - export before major changes

### Git hooks:
Create pre-push hook to export database

### Scheduled backups:
Use Task Scheduler to run export daily

---

Need help? Check Laravel documentation:
- https://laravel.com/docs/migrations
- https://laravel.com/docs/seeding
- https://laravel.com/docs/database
