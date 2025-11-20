#!/bin/bash

# Fix Git Pull Issues - Handle Local Changes
# Run this on your server

set -e

echo "========================================="
echo "Fixing Git Pull Issues"
echo "========================================="
echo ""

cd /var/www/leveler

# Step 1: Check what files have local changes
echo "Step 1: Checking for local changes..."
git status

# Step 2: Stash local changes (saves them for later if needed)
echo ""
echo "Step 2: Stashing local changes..."
git stash push -m "Local changes before pull - $(date)"

# Step 3: Pull latest code
echo ""
echo "Step 3: Pulling latest code from GitHub..."
git pull origin main

# Step 4: Check if stashed changes are needed
echo ""
echo "Step 4: Checking stashed changes..."
if git stash list | grep -q "Local changes before pull"; then
    echo "⚠ Local changes were stashed. If you need them, run:"
    echo "   git stash list"
    echo "   git stash show -p stash@{0}"
    echo "   git stash pop  (to restore them)"
    echo ""
    echo "If you don't need them, you can discard with:"
    echo "   git stash drop"
fi

echo ""
echo "========================================="
echo "Git Pull Complete!"
echo "========================================="
echo ""
echo "Now you can run:"
echo "  chmod +x setup-multiple-domains.sh"
echo "  sudo ./setup-multiple-domains.sh"
echo ""

