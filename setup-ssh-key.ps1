# Setup SSH key for passwordless access
$server = "75.119.139.18"
$user = "root"
$password = "Enter0text@@@#"
$pubkeyPath = "$env:USERPROFILE\.ssh\leveler_server.pub"
$pubkey = Get-Content $pubkeyPath

Write-Host "Copying SSH public key to server..."

# Use plink (PuTTY) if available, otherwise use ssh with expect-like approach
if (Get-Command plink -ErrorAction SilentlyContinue) {
    $pubkey | plink -ssh $user@$server -pw $password "mkdir -p ~/.ssh && chmod 700 ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"
} else {
    # Try using ssh with echo to pipe the key
    $command = "echo '$pubkey' | ssh $user@$server 'mkdir -p ~/.ssh && chmod 700 ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys'"
    Write-Host "Please run this command manually and enter password when prompted:"
    Write-Host $command
    Write-Host ""
    Write-Host "Or install plink (PuTTY) for automated password handling"
}

