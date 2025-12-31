# Simple script to run mail server setup using plink
$serverIP = "75.119.139.18"
$serverUser = "root"
$password = "Enter0text@@@#"

Write-Host "Checking for PuTTY plink..." -ForegroundColor Yellow

# Check if plink exists
$plinkPath = $null
$possiblePaths = @(
    "C:\Program Files\PuTTY\plink.exe",
    "C:\Program Files (x86)\PuTTY\plink.exe",
    "$env:ProgramFiles\PuTTY\plink.exe",
    "$env:ProgramFiles(x86)\PuTTY\plink.exe"
)

foreach ($path in $possiblePaths) {
    if (Test-Path $path) {
        $plinkPath = $path
        break
    }
}

# Also check if plink is in PATH
if (-not $plinkPath) {
    $plinkCmd = Get-Command plink -ErrorAction SilentlyContinue
    if ($plinkCmd) {
        $plinkPath = $plinkCmd.Path
    }
}

if ($plinkPath) {
    Write-Host "Found plink at: $plinkPath" -ForegroundColor Green
    Write-Host "Connecting to server..." -ForegroundColor Yellow
    
    $command = "cd /var/www/leveler && git pull origin main && chmod +x complete-mail-server-fix.sh && bash complete-mail-server-fix.sh"
    
    # Use plink with password
    & $plinkPath -ssh -pw $password -batch "$serverUser@$serverIP" $command
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "`nSetup completed successfully!" -ForegroundColor Green
    } else {
        Write-Host "`nSetup finished with exit code: $LASTEXITCODE" -ForegroundColor Yellow
    }
} else {
    Write-Host "PuTTY plink not found." -ForegroundColor Red
    Write-Host "`nInstalling PuTTY..." -ForegroundColor Yellow
    
    # Try to install PuTTY using winget
    try {
        winget install --id PuTTY.PuTTY -e --accept-package-agreements --accept-source-agreements
        Start-Sleep -Seconds 3
        
        # Try again after installation
        foreach ($path in $possiblePaths) {
            if (Test-Path $path) {
                $plinkPath = $path
                Write-Host "PuTTY installed! Using plink..." -ForegroundColor Green
                
                $command = "cd /var/www/leveler && git pull origin main && chmod +x complete-mail-server-fix.sh && bash complete-mail-server-fix.sh"
                & $plinkPath -ssh -pw $password -batch "$serverUser@$serverIP" $command
                break
            }
        }
    } catch {
        Write-Host "`nCould not install PuTTY automatically." -ForegroundColor Red
        Write-Host "`nPlease run this manually:" -ForegroundColor Yellow
        Write-Host "1. Install PuTTY: https://www.putty.org/" -ForegroundColor White
        Write-Host "2. Or run this command in PowerShell:" -ForegroundColor White
        Write-Host "   ssh $serverUser@$serverIP" -ForegroundColor Cyan
        Write-Host "3. Then paste:" -ForegroundColor White
        Write-Host "   cd /var/www/leveler && git pull origin main && chmod +x complete-mail-server-fix.sh && bash complete-mail-server-fix.sh" -ForegroundColor Cyan
    }
}
