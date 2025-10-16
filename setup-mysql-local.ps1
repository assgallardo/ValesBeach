# MySQL Quick Setup for ValesBeach

Write-Host "`n=== MySQL Local Setup ===" -ForegroundColor Cyan
Write-Host ""

# Add MySQL to PATH for this session
$env:Path += ";C:\Program Files\MySQL\MySQL Server 8.0\bin"

Write-Host "MySQL is installed and running!" -ForegroundColor Green
Write-Host ""

# Get MySQL root password
Write-Host "Enter your MySQL root password:" -ForegroundColor Yellow
Write-Host "(If you just installed MySQL, try leaving it blank and press Enter)" -ForegroundColor Gray
$rootPass = Read-Host "MySQL root password" -AsSecureString
$rootPassPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($rootPass))

# Test connection and create database
Write-Host "`nCreating 'valesbeach' database..." -ForegroundColor Yellow

if ([string]::IsNullOrWhiteSpace($rootPassPlain)) {
    # Try without password
    $result = mysql -u root -e "CREATE DATABASE IF NOT EXISTS valesbeach;" 2>&1
} else {
    # Try with password
    $result = mysql -u root -p"$rootPassPlain" -e "CREATE DATABASE IF NOT EXISTS valesbeach;" 2>&1
}

if ($LASTEXITCODE -eq 0) {
    Write-Host "Database 'valesbeach' created successfully!" -ForegroundColor Green
    Write-Host ""
    
    # Update .env file
    Write-Host "Updating .env configuration..." -ForegroundColor Yellow
    
    # Backup .env
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    Copy-Item .env ".env.backup.$timestamp"
    
    # Read and update .env
    $envContent = Get-Content .env
    $newEnvContent = @()
    $inDbSection = $false
    
    foreach ($line in $envContent) {
        if ($line -match "^DB_CONNECTION=") {
            $newEnvContent += "DB_CONNECTION=mysql"
            $newEnvContent += "DB_HOST=127.0.0.1"
            $newEnvContent += "DB_PORT=3306"
            $newEnvContent += "DB_DATABASE=valesbeach"
            $newEnvContent += "DB_USERNAME=root"
            $newEnvContent += "DB_PASSWORD=$rootPassPlain"
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
    Write-Host "Configuration updated!" -ForegroundColor Green
    Write-Host ""
    
    # Clear Laravel cache
    Write-Host "Clearing Laravel cache..." -ForegroundColor Yellow
    php artisan config:clear
    
    # Test connection
    Write-Host "`nTesting database connection..." -ForegroundColor Yellow
    php artisan db:show
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "`n=== Connection Successful! ===" -ForegroundColor Green
        Write-Host ""
        
        # Run migrations
        $migrate = Read-Host "Run database migrations now? (y/n)"
        if ($migrate -eq "y" -or $migrate -eq "Y") {
            Write-Host "`nRunning migrations..." -ForegroundColor Yellow
            php artisan migrate
            
            if ($LASTEXITCODE -eq 0) {
                Write-Host "`nMigrations completed!" -ForegroundColor Green
                
                # Seed test users
                $seed = Read-Host "`nCreate test users? (y/n)"
                if ($seed -eq "y" -or $seed -eq "Y") {
                    Write-Host "`nCreating test users..." -ForegroundColor Yellow
                    php artisan db:seed --class=TestUsersSeeder
                    
                    if ($LASTEXITCODE -eq 0) {
                        Write-Host "`n========================================" -ForegroundColor Green
                        Write-Host "  Setup Complete!" -ForegroundColor Green
                        Write-Host "========================================" -ForegroundColor Green
                        Write-Host ""
                        Write-Host "Test User Credentials:" -ForegroundColor Yellow
                        Write-Host "  Admin:   admin@valesbeach.com / admin123" -ForegroundColor White
                        Write-Host "  Manager: manager@valesbeach.com / manager123" -ForegroundColor White
                        Write-Host "  Staff:   staff@valesbeach.com / staff123" -ForegroundColor White
                        Write-Host "  Guest:   guest@valesbeach.com / guest123" -ForegroundColor White
                        Write-Host ""
                        Write-Host "Database: MySQL (Local)" -ForegroundColor Yellow
                        Write-Host "  Host: 127.0.0.1" -ForegroundColor White
                        Write-Host "  Database: valesbeach" -ForegroundColor White
                        Write-Host "  Username: root" -ForegroundColor White
                        Write-Host ""
                        Write-Host "Next steps:" -ForegroundColor Yellow
                        Write-Host "  1. Restart Laravel: php artisan serve" -ForegroundColor White
                        Write-Host "  2. Visit: http://127.0.0.1:8000" -ForegroundColor White
                        Write-Host ""
                    }
                }
            }
        }
    } else {
        Write-Host "`nConnection test failed!" -ForegroundColor Red
        Write-Host "Your .env backup is at: .env.backup.$timestamp" -ForegroundColor Yellow
    }
    
} else {
    Write-Host "`nFailed to create database!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Error details:" -ForegroundColor Yellow
    Write-Host $result -ForegroundColor Red
    Write-Host ""
    Write-Host "Troubleshooting:" -ForegroundColor Yellow
    Write-Host "1. Check if MySQL root password is correct" -ForegroundColor White
    Write-Host "2. Try running MySQL Workbench or MySQL Command Line Client" -ForegroundColor White
    Write-Host "3. You may need to reset the MySQL root password" -ForegroundColor White
    Write-Host ""
}

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
