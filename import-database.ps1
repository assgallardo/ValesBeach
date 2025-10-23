# Import Database Script for ValesBeach
# This imports a SQL file into your database

Write-Host "=== ValesBeach Database Import ===" -ForegroundColor Cyan

# Get database credentials from .env
$envFile = Get-Content .env
$dbConnection = ($envFile | Select-String "DB_CONNECTION=(.*)").Matches.Groups[1].Value
$dbHost = ($envFile | Select-String "DB_HOST=(.*)").Matches.Groups[1].Value
$dbPort = ($envFile | Select-String "DB_PORT=(.*)").Matches.Groups[1].Value
$dbDatabase = ($envFile | Select-String "DB_DATABASE=(.*)").Matches.Groups[1].Value
$dbUsername = ($envFile | Select-String "DB_USERNAME=(.*)").Matches.Groups[1].Value
$dbPassword = ($envFile | Select-String "DB_PASSWORD=(.*)").Matches.Groups[1].Value

Write-Host "Database: $dbDatabase" -ForegroundColor Yellow
Write-Host "Connection: $dbConnection" -ForegroundColor Yellow

# List available export files
Write-Host "`nAvailable database exports:" -ForegroundColor Green
if (Test-Path "database/exports") {
    $exports = Get-ChildItem "database/exports" -Filter "*.sql" | Sort-Object LastWriteTime -Descending
    $sqliteExports = Get-ChildItem "database/exports" -Filter "*.sqlite" | Sort-Object LastWriteTime -Descending
    $allExports = $exports + $sqliteExports
    
    if ($allExports.Count -eq 0) {
        Write-Host "No export files found in database/exports/" -ForegroundColor Red
        Write-Host "Run export-database.ps1 first or place a .sql file in database/exports/" -ForegroundColor Yellow
        exit
    }
    
    for ($i = 0; $i -lt $allExports.Count; $i++) {
        $size = [math]::Round($allExports[$i].Length / 1KB, 2)
        Write-Host "[$i] $($allExports[$i].Name) ($size KB) - $($allExports[$i].LastWriteTime)" -ForegroundColor Cyan
    }
    
    Write-Host "`nEnter the number of the file to import (or press Enter for the latest): " -NoNewline -ForegroundColor Yellow
    $selection = Read-Host
    
    if ([string]::IsNullOrWhiteSpace($selection)) {
        $importFile = $allExports[0].FullName
    } else {
        $importFile = $allExports[[int]$selection].FullName
    }
} else {
    Write-Host "database/exports directory not found!" -ForegroundColor Red
    exit
}

Write-Host "`nImporting: $importFile" -ForegroundColor Green

# Confirm before importing
Write-Host "`nWARNING: This will REPLACE all data in your database!" -ForegroundColor Red
Write-Host "Are you sure you want to continue? (y/n): " -NoNewline -ForegroundColor Yellow
$confirm = Read-Host

if ($confirm -ne "y") {
    Write-Host "Import cancelled." -ForegroundColor Yellow
    exit
}

if ($dbConnection -eq "mysql") {
    # MySQL Import
    Write-Host "`nImporting to MySQL database..." -ForegroundColor Green
    
    if ($dbPassword) {
        Get-Content $importFile | mysql -h $dbHost -P $dbPort -u $dbUsername -p$dbPassword $dbDatabase
    } else {
        Get-Content $importFile | mysql -h $dbHost -P $dbPort -u $dbUsername $dbDatabase
    }
    
} elseif ($dbConnection -eq "sqlite") {
    # SQLite Import
    Write-Host "`nImporting to SQLite database..." -ForegroundColor Green
    
    if ($importFile -like "*.sqlite") {
        # Direct copy for SQLite files
        Copy-Item $importFile $dbDatabase -Force
    } else {
        # Import SQL commands
        Get-Content $importFile | sqlite3 $dbDatabase
    }
}

Write-Host "`n✓ Database imported successfully!" -ForegroundColor Green
Write-Host "Run 'php artisan migrate:status' to verify." -ForegroundColor Cyan
