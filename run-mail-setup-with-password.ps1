# PowerShell script to run mail server setup with password
# Note: This uses Plink (PuTTY) or expects SSH to prompt for password

$serverIP = "75.119.139.18"
$serverUser = "root"

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Running Mail Server Setup" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "You will be prompted for your password." -ForegroundColor Yellow
Write-Host ""

# Command to run on server
$command = @"
cd /var/www/leveler && git pull origin main && chmod +x complete-mail-server-fix.sh && bash complete-mail-server-fix.sh
"@

# Try using SSH directly (will prompt for password)
Write-Host "Connecting to server and running setup..." -ForegroundColor Yellow
ssh "$serverUser@$serverIP" $command

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n✓ Mail server setup completed!" -ForegroundColor Green
} else {
    Write-Host "`n✗ Setup failed. Check the output above." -ForegroundColor Red
}

