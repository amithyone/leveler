# XAMPP MySQL InnoDB Fix Script
# This script fixes InnoDB corruption by resetting InnoDB files

Write-Host "=== XAMPP MySQL InnoDB Fix ===" -ForegroundColor Cyan
Write-Host ""

$mysqlDataPath = "C:\xampp\mysql\data"

if (-not (Test-Path $mysqlDataPath)) {
    Write-Host "Error: MySQL data directory not found at $mysqlDataPath" -ForegroundColor Red
    exit 1
}

Write-Host "Step 1: Checking if MySQL is stopped..." -ForegroundColor Yellow
$mysqlProcess = Get-Process -Name mysqld -ErrorAction SilentlyContinue
if ($mysqlProcess) {
    Write-Host "WARNING: MySQL is still running! Please stop it in XAMPP Control Panel first." -ForegroundColor Red
    Write-Host "Press any key after stopping MySQL..."
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
}

Write-Host ""
Write-Host "Step 2: Creating backup of InnoDB files..." -ForegroundColor Yellow
$backupPath = "C:\xampp\mysql\data_backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
New-Item -ItemType Directory -Path $backupPath -Force | Out-Null

$innodbFiles = @("ibdata1", "ib_logfile0", "ib_logfile1")
foreach ($file in $innodbFiles) {
    $filePath = Join-Path $mysqlDataPath $file
    if (Test-Path $filePath) {
        Copy-Item $filePath $backupPath -Force
        Write-Host "  Backed up: $file" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "Step 3: Removing corrupted InnoDB files..." -ForegroundColor Yellow
foreach ($file in $innodbFiles) {
    $filePath = Join-Path $mysqlDataPath $file
    if (Test-Path $filePath) {
        Remove-Item $filePath -Force
        Write-Host "  Removed: $file" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "=== Fix Complete! ===" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Start MySQL from XAMPP Control Panel"
Write-Host "2. MySQL will recreate the InnoDB files automatically"
Write-Host "3. Your databases should be accessible (but may need to be recreated)"
Write-Host ""
Write-Host "Backup location: $backupPath" -ForegroundColor Yellow
Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

