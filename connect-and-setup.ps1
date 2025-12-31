# PowerShell script to connect to server and run mail server setup
$serverIP = "75.119.139.18"
$serverUser = "root"
$password = "Enter0text@@@#"

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Connecting to server and running setup" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

# Check if plink (PuTTY) is available
$plinkPath = Get-Command plink -ErrorAction SilentlyContinue

if ($plinkPath) {
    Write-Host "Using PuTTY plink..." -ForegroundColor Yellow
    $command = "cd /var/www/leveler && git pull origin main && chmod +x complete-mail-server-fix.sh && bash complete-mail-server-fix.sh"
    echo y | plink -ssh -pw $password "$serverUser@$serverIP" $command
} else {
    Write-Host "Using SSH (will prompt for password)..." -ForegroundColor Yellow
    Write-Host "Note: SSH will prompt for password interactively" -ForegroundColor Yellow
    
    # Create a temporary script file to run on server
    $remoteScript = @"
cd /var/www/leveler
git pull origin main
chmod +x complete-mail-server-fix.sh
bash complete-mail-server-fix.sh
"@
    
    # Save to temp file
    $tempFile = [System.IO.Path]::GetTempFileName()
    $remoteScript | Out-File -FilePath $tempFile -Encoding ASCII
    
    Write-Host "`nConnecting to server..." -ForegroundColor Yellow
    Write-Host "Password: $password" -ForegroundColor Gray
    
    # Try using ssh with password via here-string
    # Note: This may not work as SSH doesn't accept passwords via stdin
    # We'll need to use plink or expect-like functionality
    
    # Alternative: Use PowerShell SSH module or create expect script
    Write-Host "`nPlease run this command manually in PowerShell:" -ForegroundColor Yellow
    Write-Host "ssh $serverUser@$serverIP" -ForegroundColor White
    Write-Host "Then run:" -ForegroundColor Yellow
    Write-Host $remoteScript -ForegroundColor White
}
