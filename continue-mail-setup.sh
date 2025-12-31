#!/bin/bash

# Continue mail server setup after MySQL is fixed
# This completes the configuration that was interrupted

set -e

echo "========================================="
echo "Continuing Mail Server Setup"
echo "========================================="

cd /var/www/leveler

# Configure Postfix main.cf (if not already done)
if ! grep -q "virtual_mailbox_domains" /etc/postfix/main.cf; then
    echo "Configuring Postfix main.cf..."
    
    # Backup original
    cp /etc/postfix/main.cf /etc/postfix/main.cf.backup.$(date +%Y%m%d)
    
    # Append virtual domain config
    cat >> /etc/postfix/main.cf << 'EOF'

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
EOF

    # Update mydestination
    sed -i 's/^mydestination =.*/mydestination = $myhostname, levelercc.com, biggestlogs.com, localhost.localdomain, localhost/' /etc/postfix/main.cf
    
    # Update myhostname
    sed -i 's/^myhostname =.*/myhostname = mail.levelercc.com/' /etc/postfix/main.cf || echo "myhostname = mail.levelercc.com" >> /etc/postfix/main.cf
fi

# Configure master.cf for Dovecot LDA
if ! grep -q "dovecot" /etc/postfix/master.cf; then
    echo "Configuring Postfix master.cf..."
    cat >> /etc/postfix/master.cf << 'EOF'

# Dovecot LDA
dovecot   unix  -       n       n       -       -       pipe
  flags=DRhu user=vmail:vmail argv=/usr/lib/dovecot/dovecot-lda -f ${sender} -d ${recipient}
EOF
fi

# Create vmail user if not exists
if ! id -u vmail &>/dev/null; then
    echo "Creating vmail user..."
    groupadd -g 5000 vmail || true
    useradd -g vmail -u 5000 vmail -d /var/mail/vmail -m || true
fi

mkdir -p /var/mail/vmail
chown -R vmail:vmail /var/mail/vmail

# Configure Dovecot
echo "Configuring Dovecot..."

# Ensure Dovecot configs exist
if [ ! -f /etc/dovecot/conf.d/10-mail.conf.backup ]; then
    cp /etc/dovecot/conf.d/10-mail.conf /etc/dovecot/conf.d/10-mail.conf.backup
    cat > /etc/dovecot/conf.d/10-mail.conf << 'EOF'
mail_location = maildir:/var/mail/vmail/%d/%n
namespace inbox {
  inbox = yes
}
mail_privileged_group = mail
protocol !indexer-worker {
}
EOF
fi

if [ ! -f /etc/dovecot/conf.d/10-auth.conf.backup ]; then
    cp /etc/dovecot/conf.d/10-auth.conf /etc/dovecot/conf.d/10-auth.conf.backup
    cat > /etc/dovecot/conf.d/10-auth.conf << 'EOF'
disable_plaintext_auth = yes
auth_mechanisms = plain login
!include auth-sql.conf.ext
EOF
fi

if [ ! -f /etc/dovecot/conf.d/auth-sql.conf.ext ]; then
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
fi

# Configure Dovecot master
if [ ! -f /etc/dovecot/conf.d/10-master.conf.backup ]; then
    cp /etc/dovecot/conf.d/10-master.conf /etc/dovecot/conf.d/10-master.conf.backup
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
fi

# Configure OpenDKIM
echo "Configuring OpenDKIM..."
mkdir -p /etc/opendkim/keys
chown -R opendkim:opendkim /etc/opendkim

if [ ! -f /etc/opendkim.conf.backup ]; then
    cp /etc/opendkim.conf /etc/opendkim.conf.backup
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
fi

cat > /etc/opendkim/TrustedHosts << 'EOF'
127.0.0.1
localhost
levelercc.com
biggestlogs.com
*.levelercc.com
*.biggestlogs.com
EOF

# Generate DKIM keys if they don't exist
if [ ! -f /etc/opendkim/keys/levelercc.com/default.private ]; then
    echo "Generating DKIM keys for levelercc.com..."
    mkdir -p /etc/opendkim/keys/levelercc.com
    opendkim-genkey -b 2048 -d levelercc.com -D /etc/opendkim/keys/levelercc.com -s default -v
fi

if [ ! -f /etc/opendkim/keys/biggestlogs.com/default.private ]; then
    echo "Generating DKIM keys for biggestlogs.com..."
    mkdir -p /etc/opendkim/keys/biggestlogs.com
    opendkim-genkey -b 2048 -d biggestlogs.com -D /etc/opendkim/keys/biggestlogs.com -s default -v
fi

cat > /etc/opendkim/KeyTable << 'EOF'
default._domainkey.levelercc.com levelercc.com:default:/etc/opendkim/keys/levelercc.com/default.private
default._domainkey.biggestlogs.com biggestlogs.com:default:/etc/opendkim/keys/biggestlogs.com/default.private
EOF

cat > /etc/opendkim/SigningTable << 'EOF'
*@levelercc.com default._domainkey.levelercc.com
*@biggestlogs.com default._domainkey.biggestlogs.com
EOF

chown -R opendkim:opendkim /etc/opendkim/keys
chmod 600 /etc/opendkim/keys/*/default.private

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
echo "Next steps:"
echo "1. Add DKIM keys to DNS (see MAIL_SERVER_SETUP.md)"
echo "2. Configure DNS records (MX, SPF, DMARC)"
echo "3. Set up SSL certificates"
echo "4. Create email accounts using create-email-account.sh"
echo ""

