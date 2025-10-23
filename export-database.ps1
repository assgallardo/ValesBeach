# Export Database Script for ValesBeach
# This exports your database to a SQL file that can be shared across computers

Write-Host "=== ValesBeach Database Export ===" -ForegroundColor Cyan

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

# Create exports directory if it doesn't exist
if (!(Test-Path "database/exports")) {
    New-Item -ItemType Directory -Path "database/exports" | Out-Null
}

$timestamp = Get-Date -Format "yyyy-MM-dd_HHmmss"
$exportFile = "database/exports/valesbeach_$timestamp.sql"

if ($dbConnection -eq "mysql") {
    # MySQL Export
    Write-Host "`nExporting MySQL database..." -ForegroundColor Green
    
    if ($dbPassword) {
        mysqldump -h $dbHost -P $dbPort -u $dbUsername -p$dbPassword $dbDatabase > $exportFile
    } else {
        mysqldump -h $dbHost -P $dbPort -u $dbUsername $dbDatabase > $exportFile
    }
    
} elseif ($dbConnection -eq "sqlite") {
    # SQLite Export
    Write-Host "`nExporting SQLite database..." -ForegroundColor Green
    $sqliteFile = $dbDatabase
    
    # Just copy the SQLite file
    Copy-Item $sqliteFile $exportFile.Replace(".sql", ".sqlite")
    $exportFile = $exportFile.Replace(".sql", ".sqlite")
}

if (Test-Path $exportFile) {
    $fileSize = (Get-Item $exportFile).Length / 1KB
    Write-Host "`n✓ Database exported successfully!" -ForegroundColor Green
    Write-Host "File: $exportFile" -ForegroundColor Cyan
    Write-Host "Size: $([math]::Round($fileSize, 2)) KB" -ForegroundColor Cyan
    Write-Host "`nYou can now commit this file to Git or share it with other computers." -ForegroundColor Yellow
} else {
    Write-Host "`n✗ Export failed!" -ForegroundColor Red
    Write-Host "Make sure mysqldump is installed and in your PATH." -ForegroundColor Yellow
}
