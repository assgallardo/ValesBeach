# ValesBeach Database Migration Script
# This script helps you migrate from SQLite to an online MySQL database

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  ValesBeach Database Migration Wizard" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "Current database: SQLite (local file)" -ForegroundColor Yellow
Write-Host "This wizard will help you migrate to an online MySQL database.`n" -ForegroundColor Yellow

Write-Host "Choose your database option:`n" -ForegroundColor White
Write-Host "  1. PlanetScale (Free Cloud Database)" -ForegroundColor Green
Write-Host "     - 5 GB storage free forever" -ForegroundColor Gray
Write-Host "     - No credit card required" -ForegroundColor Gray
Write-Host "     - Setup time: ~5 minutes" -ForegroundColor Gray
Write-Host ""
Write-Host "  2. Install MySQL Locally" -ForegroundColor Green
Write-Host "     - Free, unlimited storage" -ForegroundColor Gray
Write-Host "     - Good for development" -ForegroundColor Gray
Write-Host "     - Setup time: ~10 minutes" -ForegroundColor Gray
Write-Host ""
Write-Host "  3. Manual Configuration" -ForegroundColor Green
Write-Host "     - Use existing database credentials" -ForegroundColor Gray
Write-Host "     - For advanced users" -ForegroundColor Gray
Write-Host ""

$choice = Read-Host "Enter your choice (1, 2, or 3)"

switch ($choice) {
    "1" {
        Write-Host "`n=== PlanetScale Cloud Database Setup ===" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Steps to get PlanetScale credentials:" -ForegroundColor Yellow
        Write-Host "1. Open browser: https://planetscale.com" -ForegroundColor White
        Write-Host "2. Sign up with GitHub (fastest)" -ForegroundColor White
        Write-Host "3. Click 'Create Database'" -ForegroundColor White
        Write-Host "4. Name it: valesbeach" -ForegroundColor White
        Write-Host "5. Choose your region" -ForegroundColor White
        Write-Host "6. Click 'Create database'" -ForegroundColor White
        Write-Host "7. Click 'Connect' button" -ForegroundColor White
        Write-Host "8. Select 'Laravel' from dropdown" -ForegroundColor White
        Write-Host "9. Copy the credentials shown" -ForegroundColor White
        Write-Host ""
        
        $proceed = Read-Host "Do you have your PlanetScale credentials ready? (y/n)"
        
        if ($proceed -eq "y" -or $proceed -eq "Y") {
            $dbHost = Read-Host "Database Host (e.g., aws.connect.psdb.cloud)"
            $dbName = Read-Host "Database Name"
            $dbUser = Read-Host "Database Username"
            $dbPass = Read-Host "Database Password" -AsSecureString
            $dbPassPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($dbPass))
            
            $Option = "planetscale"
        } else {
            Write-Host "`nNo problem! Follow the steps above and run this script again when ready." -ForegroundColor Yellow
            Write-Host "Press any key to exit..."
            $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
            exit
        }
    }
    
    "2" {
        Write-Host "`n=== Installing MySQL Locally ===" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Installing MySQL..." -ForegroundColor Yellow
        
        winget install Oracle.MySQL --accept-package-agreements --accept-source-agreements
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "`nMySQL installed successfully!" -ForegroundColor Green
            Write-Host ""
            Write-Host "IMPORTANT: You need to set up MySQL first:" -ForegroundColor Yellow
            Write-Host "1. Open 'MySQL 8.x Command Line Client' from Start Menu" -ForegroundColor White
            Write-Host "2. Enter the root password you set during installation" -ForegroundColor White
            Write-Host "3. Run: CREATE DATABASE valesbeach;" -ForegroundColor White
            Write-Host "4. Run: exit" -ForegroundColor White
            Write-Host ""
            
            $dbHost = "127.0.0.1"
            $dbName = Read-Host "Database Name (default: valesbeach)"
            if ([string]::IsNullOrWhiteSpace($dbName)) { $dbName = "valesbeach" }
            
            $dbUser = Read-Host "MySQL Username (default: root)"
            if ([string]::IsNullOrWhiteSpace($dbUser)) { $dbUser = "root" }
            
            $dbPass = Read-Host "MySQL Root Password" -AsSecureString
            $dbPassPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($dbPass))
            
            $Option = "local"
        } else {
            Write-Host "`nMySQL installation failed or was cancelled." -ForegroundColor Red
            Write-Host "Press any key to exit..."
            $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
            exit
        }
    }
    
    "3" {
        Write-Host "`n=== Manual Database Configuration ===" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Enter your existing database credentials:" -ForegroundColor Yellow
        Write-Host ""
        
        $dbHost = Read-Host "Database Host (e.g., 127.0.0.1 or aws.connect.psdb.cloud)"
        $dbName = Read-Host "Database Name"
        $dbUser = Read-Host "Database Username"
        $dbPass = Read-Host "Database Password" -AsSecureString
        $dbPassPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($dbPass))
        
        $Option = "manual"
    }
    
    default {
        Write-Host "`nInvalid choice. Please run the script again and choose 1, 2, or 3." -ForegroundColor Red
        Write-Host "Press any key to exit..."
        $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
        exit
    }
}

# Backup .env file
Write-Host "`n=== Backing up .env file ===" -ForegroundColor Cyan
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
Copy-Item .env ".env.backup.$timestamp"
Write-Host "Backup created: .env.backup.$timestamp" -ForegroundColor Green

# Update .env file
Write-Host "`n=== Updating .env configuration ===" -ForegroundColor Cyan

$envContent = Get-Content .env
$newEnvContent = @()
$inDbSection = $false

foreach ($line in $envContent) {
    if ($line -match "^DB_CONNECTION=") {
        $newEnvContent += "DB_CONNECTION=mysql"
        $newEnvContent += "DB_HOST=$dbHost"
        $newEnvContent += "DB_PORT=3306"
        $newEnvContent += "DB_DATABASE=$dbName"
        $newEnvContent += "DB_USERNAME=$dbUser"
        $newEnvContent += "DB_PASSWORD=$dbPassPlain"
        if ($Option -eq "planetscale") {
            $newEnvContent += "MYSQL_ATTR_SSL_CA="
        }
        $inDbSection = $true
    }
    elseif ($inDbSection -and $line -match "^(DB_|MYSQL_)") {
        # Skip old DB lines
        continue
    }
    elseif ($inDbSection -and $line -match "^[A-Z]") {
        # End of DB section
        $inDbSection = $false
        $newEnvContent += $line
    }
    else {
        $newEnvContent += $line
    }
}

$newEnvContent | Set-Content .env
Write-Host "Configuration updated successfully!" -ForegroundColor Green

# Clear Laravel config cache
Write-Host "`n=== Clearing Laravel configuration cache ===" -ForegroundColor Cyan
php artisan config:clear

# Test database connection
Write-Host "`n=== Testing database connection ===" -ForegroundColor Cyan
php artisan db:show

if ($LASTEXITCODE -eq 0) {
    Write-Host "`nDatabase connection successful!" -ForegroundColor Green
    
    # Ask about running migrations
    Write-Host ""
    $migrate = Read-Host "Do you want to run migrations now? (y/n)"
    
    if ($migrate -eq "y" -or $migrate -eq "Y") {
        Write-Host "`n=== Running database migrations ===" -ForegroundColor Cyan
        php artisan migrate
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "`nMigrations completed successfully!" -ForegroundColor Green
            
            # Ask about seeding test users
            Write-Host ""
            $seed = Read-Host "Do you want to create test users? (y/n)"
            
            if ($seed -eq "y" -or $seed -eq "Y") {
                Write-Host "`n=== Creating test users ===" -ForegroundColor Cyan
                php artisan db:seed --class=TestUsersSeeder
                
                if ($LASTEXITCODE -eq 0) {
                    Write-Host "`nTest users created successfully!" -ForegroundColor Green
                    Write-Host ""
                    Write-Host "Login credentials:" -ForegroundColor Yellow
                    Write-Host "  Admin:   admin@valesbeach.com / admin123" -ForegroundColor White
                    Write-Host "  Manager: manager@valesbeach.com / manager123" -ForegroundColor White
                    Write-Host "  Staff:   staff@valesbeach.com / staff123" -ForegroundColor White
                    Write-Host "  Guest:   guest@valesbeach.com / guest123" -ForegroundColor White
                }
            }
        }
    }
    
    Write-Host "`n========================================" -ForegroundColor Green
    Write-Host "  Migration Complete!" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Your database is now online and ready to use!" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Yellow
    Write-Host "1. Restart your Laravel server: php artisan serve" -ForegroundColor White
    Write-Host "2. Test the application: http://127.0.0.1:8000" -ForegroundColor White
    Write-Host ""
    Write-Host "Database details:" -ForegroundColor Yellow
    Write-Host "  Host: $dbHost" -ForegroundColor White
    Write-Host "  Database: $dbName" -ForegroundColor White
    Write-Host "  Username: $dbUser" -ForegroundColor White
    Write-Host ""
    
} else {
    Write-Host "`nDatabase connection failed!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Troubleshooting:" -ForegroundColor Yellow
    Write-Host "1. Verify your credentials are correct" -ForegroundColor White
    Write-Host "2. Check that the database exists" -ForegroundColor White
    Write-Host "3. Ensure MySQL service is running (for local MySQL)" -ForegroundColor White
    Write-Host "4. Check firewall settings" -ForegroundColor White
    Write-Host ""
    Write-Host "Your original .env has been backed up to: .env.backup.$timestamp" -ForegroundColor Yellow
    Write-Host "You can restore it with: Copy-Item .env.backup.$timestamp .env" -ForegroundColor White
    Write-Host ""
}

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
