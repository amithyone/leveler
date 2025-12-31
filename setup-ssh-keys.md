# SSH Key Setup for Passwordless Server Access

To allow automated server access, we need to set up SSH key authentication.

## Option 1: Generate SSH Key (If you don't have one)

On your Windows machine, open PowerShell and run:

```powershell
# Generate SSH key (if you don't have one)
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"

# When prompted:
# - Press Enter to accept default location (C:\Users\LENOVO LEGION\.ssh\id_rsa)
# - Enter a passphrase (optional but recommended)
```

## Option 2: Copy Existing SSH Key to Server

```powershell
# Copy your public key to the server
type $env:USERPROFILE\.ssh\id_rsa.pub | ssh root@75.119.139.18 "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys"
```

You'll be prompted for the root password once.

## Option 3: Manual Setup

1. **Get your public key:**
```powershell
type $env:USERPROFILE\.ssh\id_rsa.pub
```

2. **Copy the output** (it starts with `ssh-rsa ...`)

3. **Connect to server manually:**
```powershell
ssh root@75.119.139.18
```

4. **On the server, run:**
```bash
mkdir -p ~/.ssh
chmod 700 ~/.ssh
nano ~/.ssh/authorized_keys
# Paste your public key here, save and exit
chmod 600 ~/.ssh/authorized_keys
```

## After Setup

Once SSH keys are set up, you can run commands without password prompts.

## Alternative: Use Password Authentication Script

If you prefer to use password authentication, you can use `sshpass` (requires installation) or provide the password interactively when connecting.

