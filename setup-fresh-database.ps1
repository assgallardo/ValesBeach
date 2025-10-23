# Fresh Database Setup Script
# Run this on any new computer to get a complete database setup

Write-Host "=== ValesBeach Fresh Database Setup ===" -ForegroundColor Cyan
Write-Host ""

# Check if .env exists
if (!(Test-Path ".env")) {
    Write-Host "⚠️  .env file not found!" -ForegroundColor Yellow
    Write-Host "Creating from .env.example..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
    Write-Host "✓ Created .env file" -ForegroundColor Green
    Write-Host ""
    Write-Host "⚠️  Please configure your database settings in .env" -ForegroundColor Yellow
    Write-Host "Then run this script again." -ForegroundColor Yellow
    exit
}

Write-Host "Step 1: Dropping all tables..." -ForegroundColor Yellow
php artisan db:wipe --force

Write-Host ""
Write-Host "Step 2: Running migrations..." -ForegroundColor Yellow
php artisan migrate --force

Write-Host ""
Write-Host "Step 3: Seeding database with data..." -ForegroundColor Yellow
php artisan db:seed --force

Write-Host ""
Write-Host "✓ Database setup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "📊 Database Status:" -ForegroundColor Cyan
php artisan migrate:status

Write-Host ""
Write-Host "👥 Test Users Created:" -ForegroundColor Cyan
Write-Host "   Guest:   guest@test.com / password" -ForegroundColor White
Write-Host "   Admin:   admin@test.com / password" -ForegroundColor White
Write-Host "   Manager: manager@test.com / password" -ForegroundColor White
Write-Host "   Staff:   staff@test.com / password" -ForegroundColor White

Write-Host ""
Write-Host "🚀 Start the server with: php artisan serve" -ForegroundColor Green
