# PowerShell script to connect and run mail server setup with password
param(
    [string]$ServerIP = "75.119.139.18",
    [string]$ServerUser = "root",
    [string]$Password = "Enter0text@@@#"
)

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Mail Server Setup Automation" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

# Command to run on server
$remoteCommand = @"
cd /var/www/leveler
git pull origin main
chmod +x complete-mail-server-fix.sh
bash complete-mail-server-fix.sh
"@

# Try plink first (PuTTY command-line tool)
$plink = Get-Command plink -ErrorAction SilentlyContinue

if ($plink) {
    Write-Host "Using PuTTY plink for password authentication..." -ForegroundColor Green
    Write-Host "Connecting to $ServerUser@$ServerIP..." -ForegroundColor Yellow
    
    # plink with password
    $plinkArgs = @(
        "-ssh",
        "-pw", $Password,
        "-batch",
        "$ServerUser@$ServerIP",
        $remoteCommand
    )
    
    & plink $plinkArgs
    $exitCode = $LASTEXITCODE
    
    if ($exitCode -eq 0) {
        Write-Host "`nSetup completed successfully!" -ForegroundColor Green
    } else {
        Write-Host "`nSetup completed with exit code: $exitCode" -ForegroundColor Yellow
    }
} else {
    Write-Host "PuTTY plink not found. Trying SSH..." -ForegroundColor Yellow
    
    # Save command to temp file
    $tempScript = Join-Path $env:TEMP "mail-setup-$(Get-Date -Format 'yyyyMMddHHmmss').sh"
    $remoteCommand | Out-File -FilePath $tempScript -Encoding ASCII -NoNewline
    
    Write-Host "`nTo install PuTTY (recommended for password auth):" -ForegroundColor Cyan
    Write-Host "winget install PuTTY.PuTTY" -ForegroundColor White
    Write-Host "`nOr run manually:" -ForegroundColor Yellow
    Write-Host "ssh $ServerUser@$ServerIP" -ForegroundColor White
    Write-Host "Then paste:" -ForegroundColor Yellow
    Write-Host $remoteCommand -ForegroundColor Gray
    
    # Try SSH anyway (will prompt for password)
    Write-Host "`nAttempting SSH connection (will prompt for password)..." -ForegroundColor Yellow
    ssh "$ServerUser@$ServerIP" $remoteCommand
}
