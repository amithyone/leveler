# PowerShell script to set up SSH keys and run mail server setup
# Run this script in PowerShell

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "SSH Key Setup and Mail Server Installation" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

# Check if SSH key exists
$sshKeyPath = "$env:USERPROFILE\.ssh\id_rsa.pub"

if (-not (Test-Path $sshKeyPath)) {
    Write-Host "SSH key not found. Generating new key..." -ForegroundColor Yellow
    ssh-keygen -t rsa -b 4096 -f "$env:USERPROFILE\.ssh\id_rsa" -N '""'
}

# Display public key
Write-Host "`nYour SSH public key:" -ForegroundColor Green
Get-Content $sshKeyPath

Write-Host "`n=========================================" -ForegroundColor Cyan
Write-Host "Next Steps:" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "1. Connect to server: ssh root@75.119.139.18" -ForegroundColor Yellow
Write-Host "2. Run these commands on the server:" -ForegroundColor Yellow
Write-Host ""
Write-Host "   mkdir -p ~/.ssh" -ForegroundColor White
Write-Host "   chmod 700 ~/.ssh" -ForegroundColor White
Write-Host "   nano ~/.ssh/authorized_keys" -ForegroundColor White
Write-Host "   # Paste the public key shown above, save and exit (Ctrl+X, Y, Enter)" -ForegroundColor Gray
Write-Host "   chmod 600 ~/.ssh/authorized_keys" -ForegroundColor White
Write-Host ""
Write-Host "3. Then run the mail setup:" -ForegroundColor Yellow
Write-Host "   cd /var/www/leveler" -ForegroundColor White
Write-Host "   git pull origin main" -ForegroundColor White
Write-Host "   chmod +x setup-mail-server.sh" -ForegroundColor White
Write-Host "   ./setup-mail-server.sh" -ForegroundColor White
Write-Host ""

# Alternative: Try to copy key directly (will prompt for password once)
$copyKey = Read-Host "Do you want to try copying the key directly? (y/n)"
if ($copyKey -eq "y" -or $copyKey -eq "Y") {
    Write-Host "Copying SSH key to server (you'll be prompted for password)..." -ForegroundColor Yellow
    Get-Content $sshKeyPath | ssh root@75.119.139.18 "mkdir -p ~/.ssh && chmod 700 ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"
    Write-Host "SSH key copied! You can now connect without password." -ForegroundColor Green
    
    Write-Host "`nRunning mail server setup..." -ForegroundColor Yellow
    ssh root@75.119.139.18 "cd /var/www/leveler && git pull origin main && chmod +x setup-mail-server.sh && ./setup-mail-server.sh"
}

