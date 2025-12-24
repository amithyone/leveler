# Run this script ONCE to set up passwordless SSH
# It will ask for your password one time, then future SSH will work without password

$server = "75.119.139.18"
$user = "root"
$pubkeyPath = "$env:USERPROFILE\.ssh\leveler_server.pub"

Write-Host "Setting up passwordless SSH access..."
Write-Host "You will be asked for your password ONCE"
Write-Host ""

$pubkey = Get-Content $pubkeyPath

# Copy public key to server
$pubkey | ssh "$user@$server" "mkdir -p ~/.ssh && chmod 700 ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"

Write-Host ""
Write-Host "✅ SSH key setup complete!"
Write-Host "Future SSH connections should work without password."

