#!/bin/bash

# Script to create email accounts for the mail server
# Usage: ./create-email-account.sh email@domain.com password

if [ -z "$1" ] || [ -z "$2" ]; then
    echo "Usage: $0 email@domain.com password"
    echo "Example: $0 admin@levelercc.com MySecurePassword123!"
    exit 1
fi

EMAIL=$1
PASSWORD=$2
DOMAIN=$(echo $EMAIL | cut -d'@' -f2)

# Validate domain
if [ "$DOMAIN" != "levelercc.com" ] && [ "$DOMAIN" != "biggestlogs.com" ]; then
    echo "Error: Domain must be levelercc.com or biggestlogs.com"
    exit 1
fi

# Check if domain exists in database
DOMAIN_EXISTS=$(mysql -N -s mailserver -e "SELECT COUNT(*) FROM virtual_domains WHERE name='$DOMAIN';")

if [ "$DOMAIN_EXISTS" -eq 0 ]; then
    echo "Error: Domain $DOMAIN not found in database. Please add it first."
    exit 1
fi

# Check if email already exists
EMAIL_EXISTS=$(mysql -N -s mailserver -e "SELECT COUNT(*) FROM virtual_users WHERE email='$EMAIL';")

if [ "$EMAIL_EXISTS" -gt 0 ]; then
    echo "Error: Email $EMAIL already exists!"
    exit 1
fi

# Generate password hash using dovecot
HASH=$(doveadm pw -s SHA512-CRYPT -p "$PASSWORD")

# Insert into database
mysql mailserver << EOF
INSERT INTO virtual_users (domain_id, password, email) 
VALUES (
  (SELECT id FROM virtual_domains WHERE name='$DOMAIN'),
  '$HASH',
  '$EMAIL'
);
EOF

if [ $? -eq 0 ]; then
    echo "✓ Email account $EMAIL created successfully!"
    echo ""
    echo "SMTP Settings:"
    echo "  Server: mail.levelercc.com"
    echo "  Port: 587 (TLS) or 465 (SSL)"
    echo "  Username: $EMAIL"
    echo "  Password: $PASSWORD"
    echo ""
    echo "IMAP Settings:"
    echo "  Server: mail.levelercc.com"
    echo "  Port: 993 (SSL)"
    echo "  Username: $EMAIL"
    echo "  Password: $PASSWORD"
    echo ""
    echo "Webmail: http://webmail.levelercc.com"
else
    echo "Error: Failed to create email account"
    exit 1
fi

