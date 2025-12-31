#!/bin/bash

# Mail Server Setup Script for levelercc.com and biggestlogs.com
# This script installs and configures Postfix, Dovecot, and Roundcube
# Server: 75.119.139.18

set -e

echo "========================================="
echo "Mail Server Setup Script"
echo "Domains: levelercc.com, biggestlogs.com"
echo "========================================="

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "Please run as root or use sudo"
    exit 1
fi

# Update system
echo "Updating system packages..."
apt-get update
apt-get upgrade -y

# Install required packages
echo "Installing mail server packages..."
apt-get install -y \
    postfix \
    dovecot-core \
    dovecot-imapd \
    dovecot-pop3d \
    dovecot-lmtpd \
    dovecot-mysql \
    mysql-server \
    apache2 \
    php \
    php-cli \
    php-fpm \
    php-mysql \
    php-mbstring \
    php-xml \
    php-curl \
    php-zip \
    php-gd \
    php-intl \
    php-json \
    certbot \
    python3-certbot-apache \
    opendkim \
    opendkim-tools

# Configure Postfix
echo "Configuring Postfix..."

# Backup original config
cp /etc/postfix/main.cf /etc/postfix/main.cf.backup

# Configure Postfix main.cf
cat > /etc/postfix/main.cf << 'EOF'
# Basic Postfix Configuration
smtpd_banner = $myhostname ESMTP $mail_name (Ubuntu)
biff = no
append_dot_mydomain = no
readme_directory = no

# TLS parameters
smtpd_tls_cert_file = /etc/ssl/certs/ssl-cert-snakeoil.pem
smtpd_tls_key_file = /etc/ssl/private/ssl-cert-snakeoil.key
smtpd_tls_security_level = may
smtpd_tls_auth_only = yes
smtp_tls_note_starttls_offer = yes
smtpd_tls_session_cache_database = btree:${data_directory}/smtpd_scache
smtp_tls_session_cache_database = btree:${data_directory}/smtp_scache

# SMTP Restrictions
smtpd_relay_restrictions = permit_mynetworks permit_sasl_authenticated defer_unauth_destination
myhostname = mail.levelercc.com
alias_maps = hash:/etc/aliases
alias_database = hash:/etc/aliases
myorigin = /etc/mailname
mydestination = $myhostname, levelercc.com, biggestlogs.com, localhost.localdomain, localhost
relayhost =
mynetworks = 127.0.0.0/8 [::ffff:127.0.0.0]/104 [::1]/128
mailbox_size_limit = 0
recipient_delimiter = +
inet_interfaces = all
inet_protocols = ipv4

# Virtual domains
virtual_mailbox_domains = mysql:/etc/postfix/mysql-virtual-mailbox-domains.cf
virtual_mailbox_maps = mysql:/etc/postfix/mysql-virtual-mailbox-maps.cf
virtual_alias_maps = mysql:/etc/postfix/mysql-virtual-alias-maps.cf
virtual_minimum_uid = 100
virtual_uid_maps = static:5000
virtual_gid_maps = static:5000

# SASL Authentication
smtpd_sasl_type = dovecot
smtpd_sasl_path = private/auth
smtpd_sasl_auth_enable = yes
smtpd_sasl_security_options = noanonymous
smtpd_sasl_local_domain = $myhostname

# Restrictions
smtpd_recipient_restrictions = permit_sasl_authenticated, permit_mynetworks, reject_unauth_destination
smtpd_sender_restrictions = permit_sasl_authenticated, permit_mynetworks

# OpenDKIM
milter_default_action = accept
milter_protocol = 2
smtpd_milters = inet:localhost:8891
non_smtpd_milters = inet:localhost:8891
EOF

# Create virtual user database
echo "Setting up virtual mail database..."
mysql -e "CREATE DATABASE IF NOT EXISTS mailserver;"
mysql -e "CREATE USER IF NOT EXISTS 'mailuser'@'localhost' IDENTIFIED BY 'CHANGE_THIS_PASSWORD';"
mysql -e "GRANT SELECT ON mailserver.* TO 'mailuser'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Create mail tables
mysql mailserver << 'EOF'
CREATE TABLE IF NOT EXISTS virtual_domains (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS virtual_users (
  id INT NOT NULL AUTO_INCREMENT,
  domain_id INT NOT NULL,
  password VARCHAR(106) NOT NULL,
  email VARCHAR(120) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  FOREIGN KEY (domain_id) REFERENCES virtual_domains(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS virtual_aliases (
  id INT NOT NULL AUTO_INCREMENT,
  domain_id INT NOT NULL,
  source VARCHAR(100) NOT NULL,
  destination VARCHAR(100) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (domain_id) REFERENCES virtual_domains(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOF

# Insert domains
mysql mailserver << 'EOF'
INSERT INTO virtual_domains (name) VALUES ('levelercc.com'), ('biggestlogs.com');
EOF

# Create Postfix MySQL config files
cat > /etc/postfix/mysql-virtual-mailbox-domains.cf << 'EOF'
user = mailuser
password = CHANGE_THIS_PASSWORD
hosts = 127.0.0.1
dbname = mailserver
query = SELECT 1 FROM virtual_domains WHERE name='%s'
EOF

cat > /etc/postfix/mysql-virtual-mailbox-maps.cf << 'EOF'
user = mailuser
password = CHANGE_THIS_PASSWORD
hosts = 127.0.0.1
dbname = mailserver
query = SELECT 1 FROM virtual_users WHERE email='%s'
EOF

cat > /etc/postfix/mysql-virtual-alias-maps.cf << 'EOF'
user = mailuser
password = CHANGE_THIS_PASSWORD
hosts = 127.0.0.1
dbname = mailserver
query = SELECT destination FROM virtual_aliases WHERE source='%s'
EOF

# Set permissions
chmod 600 /etc/postfix/mysql-virtual-*.cf
chown root:postfix /etc/postfix/mysql-virtual-*.cf

# Configure master.cf
cat >> /etc/postfix/master.cf << 'EOF'

# Dovecot LDA
dovecot   unix  -       n       n       -       -       pipe
  flags=DRhu user=vmail:vmail argv=/usr/lib/dovecot/dovecot-lda -f ${sender} -d ${recipient}
EOF

# Create vmail user
echo "Creating vmail user..."
groupadd -g 5000 vmail || true
useradd -g vmail -u 5000 vmail -d /var/mail/vmail -m || true
mkdir -p /var/mail/vmail
chown -R vmail:vmail /var/mail/vmail

# Configure Dovecot
echo "Configuring Dovecot..."

cat > /etc/dovecot/dovecot.conf << 'EOF'
!include_try /usr/share/dovecot/protocols.d/*.protocol
protocols = imap pop3 lmtp
!include_try /etc/dovecot/conf.d/*.conf
!include_try local.conf
EOF

cat > /etc/dovecot/conf.d/10-mail.conf << 'EOF'
mail_location = maildir:/var/mail/vmail/%d/%n
namespace inbox {
  inbox = yes
}
mail_privileged_group = mail
protocol !indexer-worker {
}
EOF

cat > /etc/dovecot/conf.d/10-auth.conf << 'EOF'
disable_plaintext_auth = yes
auth_mechanisms = plain login
!include auth-sql.conf.ext
EOF

cat > /etc/dovecot/conf.d/auth-sql.conf.ext << 'EOF'
passdb {
  driver = sql
  args = /etc/dovecot/dovecot-sql.conf.ext
}
userdb {
  driver = static
  args = uid=vmail gid=vmail home=/var/mail/vmail/%d/%n
}
EOF

cat > /etc/dovecot/dovecot-sql.conf.ext << 'EOF'
driver = mysql
connect = host=127.0.0.1 dbname=mailserver user=mailuser password=CHANGE_THIS_PASSWORD
default_pass_scheme = SHA512-CRYPT
password_query = SELECT email as user, password FROM virtual_users WHERE email='%u';
EOF

chmod 600 /etc/dovecot/dovecot-sql.conf.ext
chown root:dovecot /etc/dovecot/dovecot-sql.conf.ext

cat > /etc/dovecot/conf.d/10-master.conf << 'EOF'
service imap-login {
  inet_listener imap {
    port = 143
  }
  inet_listener imaps {
    port = 993
    ssl = yes
  }
}

service pop3-login {
  inet_listener pop3 {
    port = 110
  }
  inet_listener pop3s {
    port = 995
    ssl = yes
  }
}

service lmtp {
  unix_listener /var/spool/postfix/private/dovecot-lmtp {
    mode = 0600
    user = postfix
    group = postfix
  }
}

service auth {
  unix_listener /var/spool/postfix/private/auth {
    mode = 0666
    user = postfix
    group = postfix
  }
  unix_listener auth-userdb {
    mode = 0600
    user = vmail
    group = vmail
  }
  user = dovecot
}

service auth-worker {
  user = vmail
}
EOF

cat > /etc/dovecot/conf.d/10-ssl.conf << 'EOF'
ssl = required
ssl_cert = </etc/ssl/certs/ssl-cert-snakeoil.pem
ssl_key = </etc/ssl/private/ssl-cert-snakeoil.key
EOF

# Configure OpenDKIM
echo "Configuring OpenDKIM..."

mkdir -p /etc/opendkim/keys
chown -R opendkim:opendkim /etc/opendkim

cat > /etc/opendkim.conf << 'EOF'
Syslog                  yes
SyslogSuccess           yes
LogWhy                  yes
Canonicalization        relaxed/simple
Mode                    sv
SubDomains              yes
Socket                  inet:8891@localhost
PidFile                 /var/run/opendkim/opendkim.pid
UMask                   022
UserID                  opendkim:opendkim
KeyTable                /etc/opendkim/KeyTable
SigningTable            refile:/etc/opendkim/SigningTable
ExternalIgnoreList      refile:/etc/opendkim/TrustedHosts
InternalHosts           refile:/etc/opendkim/TrustedHosts
EOF

cat > /etc/opendkim/TrustedHosts << 'EOF'
127.0.0.1
localhost
levelercc.com
biggestlogs.com
*.levelercc.com
*.biggestlogs.com
EOF

cat > /etc/opendkim/KeyTable << 'EOF'
default._domainkey.levelercc.com levelercc.com:default:/etc/opendkim/keys/levelercc.com/default.private
default._domainkey.biggestlogs.com biggestlogs.com:default:/etc/opendkim/keys/biggestlogs.com/default.private
EOF

cat > /etc/opendkim/SigningTable << 'EOF'
*@levelercc.com default._domainkey.levelercc.com
*@biggestlogs.com default._domainkey.biggestlogs.com
EOF

# Generate DKIM keys
mkdir -p /etc/opendkim/keys/levelercc.com
mkdir -p /etc/opendkim/keys/biggestlogs.com

opendkim-genkey -b 2048 -d levelercc.com -D /etc/opendkim/keys/levelercc.com -s default -v
opendkim-genkey -b 2048 -d biggestlogs.com -D /etc/opendkim/keys/biggestlogs.com -s default -v

chown -R opendkim:opendkim /etc/opendkim/keys
chmod 600 /etc/opendkim/keys/*/default.private

# Install Roundcube
echo "Installing Roundcube..."

cd /var/www
wget -q https://github.com/roundcube/roundcubemail/releases/download/1.6.5/roundcubemail-1.6.5-complete.tar.gz
tar -xzf roundcubemail-1.6.5-complete.tar.gz
mv roundcubemail-1.6.5 webmail
rm roundcubemail-1.6.5-complete.tar.gz

chown -R www-data:www-data /var/www/webmail
chmod -R 755 /var/www/webmail

# Configure Apache for Roundcube
cat > /etc/apache2/sites-available/webmail.conf << 'EOF'
<VirtualHost *:80>
    ServerName webmail.levelercc.com
    DocumentRoot /var/www/webmail/public_html

    <Directory /var/www/webmail/public_html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/webmail_error.log
    CustomLog ${APACHE_LOG_DIR}/webmail_access.log combined
</VirtualHost>
EOF

a2ensite webmail.conf
a2enmod rewrite
systemctl reload apache2

# Restart services
echo "Restarting services..."
systemctl restart postfix
systemctl restart dovecot
systemctl restart opendkim
systemctl enable postfix
systemctl enable dovecot
systemctl enable opendkim

# Display DKIM keys
echo ""
echo "========================================="
echo "DKIM Keys Generated"
echo "========================================="
echo ""
echo "levelercc.com DKIM Public Key:"
cat /etc/opendkim/keys/levelercc.com/default.txt
echo ""
echo "biggestlogs.com DKIM Public Key:"
cat /etc/opendkim/keys/biggestlogs.com/default.txt
echo ""

echo "========================================="
echo "Mail Server Setup Complete!"
echo "========================================="
echo ""
echo "IMPORTANT NEXT STEPS:"
echo "1. Change MySQL password in these files:"
echo "   - /etc/postfix/mysql-virtual-*.cf"
echo "   - /etc/dovecot/dovecot-sql.conf.ext"
echo ""
echo "2. Configure DNS records (see MAIL_SERVER_SETUP.md)"
echo ""
echo "3. Set up SSL certificates:"
echo "   certbot --apache -d webmail.levelercc.com"
echo ""
echo "4. Create email accounts using the provided script"
echo ""
echo "5. Configure Roundcube at: http://webmail.levelercc.com"
echo ""

