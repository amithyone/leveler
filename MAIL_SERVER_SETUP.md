# Mail Server Setup Guide

Complete guide for setting up mail server with Postfix, Dovecot, and Roundcube for **levelercc.com** and **biggestlogs.com**.

## Server Information
- **Server IP**: 75.119.139.18
- **Mail Hostname**: mail.levelercc.com
- **Webmail URL**: webmail.levelercc.com

## Prerequisites

1. Root access to the server
2. DNS access for both domains
3. Ports to open: 25 (SMTP), 587 (SMTP Submission), 993 (IMAPS), 995 (POP3S), 80/443 (HTTP/HTTPS)

## Installation Steps

### Step 1: Run the Setup Script

```bash
ssh root@75.119.139.18
cd /var/www/leveler
chmod +x setup-mail-server.sh
./setup-mail-server.sh
```

### Step 2: Configure MySQL Password

After installation, change the default password in these files:

```bash
# Edit Postfix MySQL configs
nano /etc/postfix/mysql-virtual-mailbox-domains.cf
nano /etc/postfix/mysql-virtual-mailbox-maps.cf
nano /etc/postfix/mysql-virtual-alias-maps.cf

# Edit Dovecot MySQL config
nano /etc/dovecot/dovecot-sql.conf.ext
```

Replace `CHANGE_THIS_PASSWORD` with a strong password in all files.

Also update MySQL user password:
```bash
mysql -e "ALTER USER 'mailuser'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';"
mysql -e "FLUSH PRIVILEGES;"
```

### Step 3: DNS Configuration

Add these DNS records for **both domains** (levelercc.com and biggestlogs.com):

#### A Records
```
mail.levelercc.com    A    75.119.139.18
webmail.levelercc.com A    75.119.139.18
```

#### MX Records
```
levelercc.com    MX    10    mail.levelercc.com
biggestlogs.com  MX    10    mail.levelercc.com
```

#### SPF Record (TXT)
```
levelercc.com    TXT    "v=spf1 mx a:mail.levelercc.com ip4:75.119.139.18 ~all"
biggestlogs.com  TXT    "v=spf1 mx a:mail.biggestlogs.com ip4:75.119.139.18 ~all"
```

#### DKIM Record (TXT)
After running the setup script, you'll get DKIM keys. Add them as TXT records:

For levelercc.com:
```
default._domainkey.levelercc.com    TXT    "v=DKIM1; k=rsa; p=YOUR_PUBLIC_KEY_HERE"
```

For biggestlogs.com:
```
default._domainkey.biggestlogs.com  TXT    "v=DKIM1; k=rsa; p=YOUR_PUBLIC_KEY_HERE"
```

To get your DKIM public keys:
```bash
cat /etc/opendkim/keys/levelercc.com/default.txt
cat /etc/opendkim/keys/biggestlogs.com/default.txt
```

#### DMARC Record (TXT)
```
_dmarc.levelercc.com    TXT    "v=DMARC1; p=quarantine; rua=mailto:admin@levelercc.com"
_dmarc.biggestlogs.com  TXT    "v=DMARC1; p=quarantine; rua=mailto:admin@biggestlogs.com"
```

### Step 4: SSL Certificate Setup

```bash
# For webmail
certbot --apache -d webmail.levelercc.com

# For mail server (if needed)
certbot certonly --standalone -d mail.levelercc.com
```

Update Dovecot SSL config:
```bash
nano /etc/dovecot/conf.d/10-ssl.conf
```

Change to:
```
ssl_cert = </etc/letsencrypt/live/mail.levelercc.com/fullchain.pem
ssl_key = </etc/letsencrypt/live/mail.levelercc.com/privkey.pem
```

### Step 5: Create Email Accounts

Use the provided script or MySQL directly:

```bash
# Create email account script
mysql mailserver << EOF
INSERT INTO virtual_users (domain_id, password, email) 
VALUES (
  (SELECT id FROM virtual_domains WHERE name='levelercc.com'),
  '{SHA512-CRYPT}\$6\$rounds=5000\$saltstring\$hashedpassword',
  'admin@levelercc.com'
);
EOF
```

Or use the provided helper script:
```bash
./create-email-account.sh admin@levelercc.com
```

### Step 6: Configure Roundcube

1. Access webmail: `http://webmail.levelercc.com`
2. Follow the installation wizard
3. Database configuration:
   - Database type: MySQL
   - Database name: `roundcube` (create it first)
   - Database user: `roundcube` (create user)
   - Database password: (set a password)

Create Roundcube database:
```bash
mysql -e "CREATE DATABASE roundcube;"
mysql -e "CREATE USER 'roundcube'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD';"
mysql -e "GRANT ALL PRIVILEGES ON roundcube.* TO 'roundcube'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"
```

Import Roundcube schema:
```bash
mysql roundcube < /var/www/webmail/SQL/mysql.initial.sql
```

### Step 7: Configure Laravel (.env)

Update your Laravel `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.levelercc.com
MAIL_PORT=587
MAIL_USERNAME=noreply@levelercc.com
MAIL_PASSWORD=YOUR_EMAIL_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@levelercc.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Firewall Configuration

Ensure these ports are open:

```bash
ufw allow 25/tcp
ufw allow 587/tcp
ufw allow 993/tcp
ufw allow 995/tcp
ufw allow 80/tcp
ufw allow 443/tcp
```

## Testing Email

### Test SMTP Connection
```bash
telnet mail.levelercc.com 25
```

### Test Email Sending
```bash
echo "Test email" | mail -s "Test Subject" your-email@example.com
```

### Check Mail Logs
```bash
tail -f /var/log/mail.log
tail -f /var/log/mail.err
```

## Troubleshooting

### Check Service Status
```bash
systemctl status postfix
systemctl status dovecot
systemctl status opendkim
```

### Check Postfix Configuration
```bash
postfix check
postconf -n
```

### Check Dovecot Configuration
```bash
dovecot -n
```

### View Mail Queue
```bash
postqueue -p
```

### Test MySQL Connection
```bash
mysql -u mailuser -p mailserver
```

## Security Best Practices

1. **Change all default passwords**
2. **Use strong passwords** for email accounts
3. **Enable fail2ban** to prevent brute force attacks
4. **Regular updates**: `apt-get update && apt-get upgrade`
5. **Monitor logs** regularly
6. **Use SSL/TLS** for all connections
7. **Configure SPF, DKIM, DMARC** properly

## Email Account Management

### Create Email Account Script

Create `/usr/local/bin/create-email.sh`:

```bash
#!/bin/bash
if [ -z "$1" ] || [ -z "$2" ]; then
    echo "Usage: create-email.sh email@domain.com password"
    exit 1
fi

EMAIL=$1
PASSWORD=$2
DOMAIN=$(echo $EMAIL | cut -d'@' -f2)

# Generate password hash
HASH=$(doveadm pw -s SHA512-CRYPT -p "$PASSWORD")

mysql mailserver << EOF
INSERT INTO virtual_users (domain_id, password, email) 
VALUES (
  (SELECT id FROM virtual_domains WHERE name='$DOMAIN'),
  '$HASH',
  '$EMAIL'
);
EOF

echo "Email account $EMAIL created successfully!"
```

Make it executable:
```bash
chmod +x /usr/local/bin/create-email.sh
```

Usage:
```bash
create-email.sh admin@levelercc.com "StrongPassword123!"
```

## Maintenance

### Backup Email Database
```bash
mysqldump mailserver > /backup/mailserver-$(date +%Y%m%d).sql
```

### Clean Old Emails
```bash
# Find old emails (older than 90 days)
find /var/mail/vmail -type f -mtime +90 -delete
```

## Support

For issues, check:
- `/var/log/mail.log` - Postfix logs
- `/var/log/mail.err` - Postfix errors
- `/var/log/dovecot.log` - Dovecot logs
- `/var/log/apache2/error.log` - Apache/Roundcube errors

