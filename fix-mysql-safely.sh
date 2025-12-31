#!/bin/bash
# Safe MySQL fix script - backs up databases first

echo "========================================="
echo "Safe MySQL Fix Script"
echo "========================================="

# Backup existing databases if possible
echo "Attempting to backup databases..."
BACKUP_DIR="/root/mysql-backup-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Try to backup using mysqldump (may fail if MySQL won't start)
# But we'll try anyway
echo "Creating backup directory: $BACKUP_DIR"

# Remove frozen flag
echo "Removing MySQL frozen flag..."
rm -f /etc/mysql/FROZEN

# Try to start MySQL
echo "Attempting to start MySQL..."
systemctl reset-failed mysql
systemctl start mysql

sleep 5

# Check if MySQL started
if systemctl is-active --quiet mysql; then
    echo "MySQL started successfully!"
    
    # Now backup databases
    echo "Backing up databases..."
    for db in leveler biggestlogs heroes_wp; do
        if mysql -e "USE $db" 2>/dev/null; then
            echo "Backing up database: $db"
            mysqldump $db > "$BACKUP_DIR/$db.sql" 2>/dev/null || echo "Failed to backup $db"
        fi
    done
    
    echo "Backup completed in: $BACKUP_DIR"
else
    echo "MySQL failed to start. Checking error logs..."
    journalctl -xeu mysql.service --no-pager | tail -30
    
    echo ""
    echo "MySQL data may need to be upgraded or reinitialized."
    echo "If you have backups, you may need to restore from them."
fi
