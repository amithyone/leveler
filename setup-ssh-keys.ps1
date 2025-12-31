# PowerShell script to set up SSH keys for passwordless access
# This will allow automated server access without passwords

$serverIP = "75.119.139.18"
$serverUser = "root"
$sshKeyPath = "$env:USERPROFILE\.ssh\id_rsa"

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Setting up SSH Key Authentication" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

# Check if SSH key exists
if (-not (Test-Path $sshKeyPath)) {
    Write-Host "Generating SSH key..." -ForegroundColor Yellow
    ssh-keygen -t rsa -b 4096 -f $sshKeyPath -N '""' -C "leveler-server"
}

# Read public key
$publicKey = Get-Content "$sshKeyPath.pub"

Write-Host "`nCopying SSH key to server..." -ForegroundColor Yellow
Write-Host "You will be prompted for your password once." -ForegroundColor Yellow

# Copy key to server
$publicKey | ssh "$serverUser@$serverIP" "mkdir -p ~/.ssh && chmod 700 ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n✓ SSH key copied successfully!" -ForegroundColor Green
    Write-Host "You can now connect without password." -ForegroundColor Green
    
    # Test connection
    Write-Host "`nTesting passwordless connection..." -ForegroundColor Yellow
    ssh -o BatchMode=yes -o ConnectTimeout=5 "$serverUser@$serverIP" "echo 'Connection successful!'"
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Passwordless SSH is working!" -ForegroundColor Green
        Write-Host "`nNow running mail server setup..." -ForegroundColor Yellow
        ssh "$serverUser@$serverIP" "cd /var/www/leveler && git pull origin main && chmod +x complete-mail-server-fix.sh && bash complete-mail-server-fix.sh"
    } else {
        Write-Host "SSH key setup completed. Please test manually." -ForegroundColor Yellow
    }
} else {
    Write-Host "`n✗ Failed to copy SSH key. Please try manually." -ForegroundColor Red
    Write-Host "`nManual steps:" -ForegroundColor Yellow
    Write-Host "1. Copy this public key:" -ForegroundColor White
    Write-Host $publicKey -ForegroundColor Gray
    Write-Host "`n2. Connect to server: ssh $serverUser@$serverIP" -ForegroundColor White
    Write-Host "3. Run: mkdir -p ~/.ssh && chmod 700 ~/.ssh" -ForegroundColor White
    Write-Host "4. Run: nano ~/.ssh/authorized_keys" -ForegroundColor White
    Write-Host "5. Paste the key above, save and exit" -ForegroundColor White
    Write-Host "6. Run: chmod 600 ~/.ssh/authorized_keys" -ForegroundColor White
}

