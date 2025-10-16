# ValesBeach - Quick Database Migration Script
# This script helps you switch from SQLite to an online database

param(
    [Parameter()]
    [ValidateSet('local', 'cloud', 'planetscale', 'railway')]
    [string]$Option = ''
)

Write-Host "`n" -NoNewline
Write-Host "╔════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  ValesBeach - Database Migration Wizard   ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

if ([string]::IsNullOrWhiteSpace($Option)) {
    Write-Host "Choose your database setup option:" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "  1. Local MySQL Server (Development)" -ForegroundColor White
    Write-Host "     - Install MySQL on this PC" -ForegroundColor Gray
    Write-Host "     - Free, unlimited storage" -ForegroundColor Gray
    Write-Host "     - Good for development/testing" -ForegroundColor Gray
    Write-Host ""
    Write-Host "  2. PlanetScale (Cloud - FREE Tier)" -ForegroundColor White
    Write-Host "     - 5GB storage free forever" -ForegroundColor Gray
    Write-Host "     - Production-ready MySQL" -ForegroundColor Gray
    Write-Host "     - No server management" -ForegroundColor Gray
    Write-Host ""
    Write-Host "  3. Manual Configuration" -ForegroundColor White
    Write-Host "     - I'll configure .env myself" -ForegroundColor Gray
    Write-Host ""
    
    $choice = Read-Host "Enter choice (1, 2, or 3)"
    
    switch ($choice) {
        "1" { $Option = "local" }
        "2" { $Option = "planetscale" }
        "3" { $Option = "manual" }
        default {
            Write-Host "Invalid choice. Exiting." -ForegroundColor Red
            exit
        }
    }
}

Write-Host ""

# Handle each option
switch ($Option) {
    "local" {
        Write-Host "🔧 Setting up LOCAL MySQL Server..." -ForegroundColor Cyan
        Write-Host ""
        
        # Check if MySQL is installed
        $mysqlCmd = Get-Command mysql -ErrorAction SilentlyContinue
        
        if (!$mysqlCmd) {
            Write-Host "MySQL not found. Installing..." -ForegroundColor Yellow
            Write-Host "This will open the MySQL installer." -ForegroundColor White
            Write-Host "Please set a ROOT PASSWORD during installation!`n" -ForegroundColor Yellow
            
            $confirm = Read-Host "Continue with installation? (y/n)"
            if ($confirm -ne 'y') {
                Write-Host "Installation cancelled." -ForegroundColor Red
                exit
            }
            
            winget install Oracle.MySQL
            
            Write-Host "`n✅ MySQL installation started." -ForegroundColor Green
            Write-Host "⏸ Please complete the installation wizard, then re-run this script.`n" -ForegroundColor Yellow
            exit
        }
        
        Write-Host "✅ MySQL found!`n" -ForegroundColor Green
        
        # Get credentials
        $dbName = Read-Host "Enter database name (default: valesbeach)"
        if ([string]::IsNullOrWhiteSpace($dbName)) { $dbName = "valesbeach" }
        
        $dbUser = Read-Host "Enter MySQL username (default: root)"
        if ([string]::IsNullOrWhiteSpace($dbUser)) { $dbUser = "root" }
        
        $dbPass = Read-Host "Enter MySQL password" -AsSecureString
        $dbPassPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto(
            [Runtime.InteropServices.Marshal]::SecureStringToBSTR($dbPass)
        )
        
        # Create database
        Write-Host "`nCreating database '$dbName'..." -ForegroundColor Yellow
        $createDbCmd = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        
        try {
            mysql -u $dbUser "-p$dbPassPlain" -e $createDbCmd 2>$null
            Write-Host "✅ Database created!`n" -ForegroundColor Green
        } catch {
            Write-Host "⚠ Could not create database. It may already exist or credentials are wrong.`n" -ForegroundColor Yellow
        }
        
        # Update .env
        Write-Host "Updating .env file..." -ForegroundColor Yellow
        $envPath = "$PSScriptRoot\.env"
        $envBackup = "$PSScriptRoot\.env.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"
        
        # Backup .env
        Copy-Item $envPath $envBackup
        Write-Host "✅ Backup created: $envBackup" -ForegroundColor Green
        
        # Read current .env
        $envContent = Get-Content $envPath -Raw
        
        # Replace database config
        $envContent = $envContent -replace 'DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql'
        $envContent = $envContent -replace 'DB_DATABASE=.*', "DB_DATABASE=$dbName"
        
        # Add MySQL config if not present
        if ($envContent -notmatch 'DB_HOST=') {
            $envContent = $envContent -replace '(DB_CONNECTION=mysql)', "`$1`nDB_HOST=127.0.0.1`nDB_PORT=3306"
        }
        if ($envContent -notmatch 'DB_USERNAME=') {
            $envContent = $envContent -replace '(DB_PORT=\d+)', "`$1`nDB_USERNAME=$dbUser"
        }
        if ($envContent -notmatch 'DB_PASSWORD=') {
            $envContent = $envContent -replace '(DB_USERNAME=.*)', "`$1`nDB_PASSWORD=$dbPassPlain"
        }
        
        Set-Content $envPath -Value $envContent
        Write-Host "✅ .env updated`n" -ForegroundColor Green
    }
    
    "planetscale" {
        Write-Host "🌐 Setting up PLANETSCALE Cloud Database..." -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Follow these steps:" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "1. Go to: https://planetscale.com" -ForegroundColor White
        Write-Host "2. Sign up/Login (use GitHub for quick signup)" -ForegroundColor White
        Write-Host "3. Click 'Create Database'" -ForegroundColor White
        Write-Host "4. Name it: valesbeach" -ForegroundColor White
        Write-Host "5. Choose region closest to you" -ForegroundColor White
        Write-Host "6. Click 'Connect' → Select 'Laravel'" -ForegroundColor White
        Write-Host "7. Copy the connection details`n" -ForegroundColor White
        
        Write-Host "Press any key when you have the credentials ready..." -ForegroundColor Yellow
        $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
        Write-Host ""
        
        # Get credentials
        Write-Host "Enter your PlanetScale connection details:" -ForegroundColor Yellow
        $dbHost = Read-Host "Database Host (e.g., aws.connect.psdb.cloud)"
        $dbName = Read-Host "Database Name"
        $dbUser = Read-Host "Username"
        $dbPass = Read-Host "Password"
        
        # Update .env
        Write-Host "`nUpdating .env file..." -ForegroundColor Yellow
        $envPath = "$PSScriptRoot\.env"
        $envBackup = "$PSScriptRoot\.env.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"
        
        Copy-Item $envPath $envBackup
        Write-Host "✅ Backup created: $envBackup" -ForegroundColor Green
        
        $envContent = Get-Content $envPath -Raw
        $envContent = $envContent -replace 'DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql'
        $envContent = $envContent -replace 'DB_DATABASE=.*', "DB_DATABASE=$dbName"
        
        # Add/update config
        if ($envContent -notmatch 'DB_HOST=') {
            $envContent = $envContent -replace '(DB_CONNECTION=mysql)', "`$1`nDB_HOST=$dbHost`nDB_PORT=3306"
        } else {
            $envContent = $envContent -replace 'DB_HOST=.*', "DB_HOST=$dbHost"
        }
        
        if ($envContent -notmatch 'DB_USERNAME=') {
            $envContent = $envContent -replace '(DB_PORT=\d+)', "`$1`nDB_USERNAME=$dbUser"
        } else {
            $envContent = $envContent -replace 'DB_USERNAME=.*', "DB_USERNAME=$dbUser"
        }
        
        if ($envContent -notmatch 'DB_PASSWORD=') {
            $envContent = $envContent -replace '(DB_USERNAME=.*)', "`$1`nDB_PASSWORD=$dbPass"
        } else {
            $envContent = $envContent -replace 'DB_PASSWORD=.*', "DB_PASSWORD=$dbPass"
        }
        
        # Add SSL config for PlanetScale
        if ($envContent -notmatch 'MYSQL_ATTR_SSL_CA=') {
            $envContent += "`nMYSQL_ATTR_SSL_CA=/etc/ssl/cert.pem"
        }
        
        Set-Content $envPath -Value $envContent
        Write-Host "✅ .env updated`n" -ForegroundColor Green
    }
    
    "manual" {
        Write-Host "📝 Manual Configuration Mode" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Edit your .env file manually:" -ForegroundColor Yellow
        Write-Host "  Location: C:\Users\sethy\ValesBeach\.env`n" -ForegroundColor White
        Write-Host "Update these lines:" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "DB_CONNECTION=mysql" -ForegroundColor Gray
        Write-Host "DB_HOST=your_host" -ForegroundColor Gray
        Write-Host "DB_PORT=3306" -ForegroundColor Gray
        Write-Host "DB_DATABASE=your_database" -ForegroundColor Gray
        Write-Host "DB_USERNAME=your_username" -ForegroundColor Gray
        Write-Host "DB_PASSWORD=your_password" -ForegroundColor Gray
        Write-Host ""
        
        $openEnv = Read-Host "Open .env file now? (y/n)"
        if ($openEnv -eq 'y') {
            notepad "$PSScriptRoot\.env"
        }
        
        Write-Host "`nAfter editing, press any key to continue..." -ForegroundColor Yellow
        $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
    }
}

# Common post-setup steps
if ($Option -ne "manual") {
    Write-Host "═══════════════════════════════════════════" -ForegroundColor Cyan
    Write-Host "Running Laravel Commands..." -ForegroundColor Yellow
    Write-Host ""
    
    # Clear config
    Write-Host "Clearing config cache..." -ForegroundColor White
    php artisan config:clear
    php artisan cache:clear
    
    # Test connection
    Write-Host "`nTesting database connection..." -ForegroundColor White
    php artisan db:show
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Database connection successful!`n" -ForegroundColor Green
        
        # Ask about migrations
        Write-Host "Do you want to run migrations now? (y/n)" -ForegroundColor Yellow
        $runMigrations = Read-Host
        
        if ($runMigrations -eq 'y') {
            Write-Host "`nRunning migrations..." -ForegroundColor White
            php artisan migrate
            
            # Ask about seeders
            Write-Host "`nDo you want to create test users? (y/n)" -ForegroundColor Yellow
            $runSeeders = Read-Host
            
            if ($runSeeders -eq 'y') {
                Write-Host "Creating test users..." -ForegroundColor White
                php artisan db:seed --class=TestUsersSeeder
            }
        }
        
        Write-Host ""
        Write-Host "╔════════════════════════════════════════════╗" -ForegroundColor Green
        Write-Host "║     Database Migration Complete! ✅       ║" -ForegroundColor Green
        Write-Host "╚════════════════════════════════════════════╝" -ForegroundColor Green
        Write-Host ""
        Write-Host "Your application is now using an online database!" -ForegroundColor White
        Write-Host ""
        Write-Host "Next steps:" -ForegroundColor Yellow
        Write-Host "  1. Test your application: http://127.0.0.1:8000" -ForegroundColor White
        Write-Host "  2. Check database tables are created" -ForegroundColor White
        Write-Host "  3. Login with test accounts (see TEST_USERS_CREDENTIALS.md)" -ForegroundColor White
        Write-Host ""
        
    } else {
        Write-Host "❌ Database connection failed!`n" -ForegroundColor Red
        Write-Host "Please check:" -ForegroundColor Yellow
        Write-Host "  • Database credentials are correct" -ForegroundColor White
        Write-Host "  • Database server is running" -ForegroundColor White
        Write-Host "  • Firewall allows connection" -ForegroundColor White
        Write-Host "  • .env file is properly configured`n" -ForegroundColor White
    }
} else {
    Write-Host "`nRun these commands after editing .env:" -ForegroundColor Yellow
    Write-Host "  php artisan config:clear" -ForegroundColor White
    Write-Host "  php artisan db:show" -ForegroundColor White
    Write-Host "  php artisan migrate" -ForegroundColor White
    Write-Host "  php artisan db:seed --class=TestUsersSeeder`n" -ForegroundColor White
}

Write-Host "═══════════════════════════════════════════`n" -ForegroundColor Cyan
