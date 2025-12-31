#!/bin/bash
# Setup SSL certificates for all domains on the server

echo "========================================="
echo "Setting up SSL for All Domains"
echo "========================================="

# List of all domains
DOMAINS=(
    "levelercc.com"
    "www.levelercc.com"
    "biggestlogs.com"
    "www.biggestlogs.com"
    "mail.levelercc.com"
    "mail.biggestlogs.com"
    "heroes.levelercc.com"
    "db.biggestlogs.com"
    "server.biggestlogs.com"
)

# Enable SSL module
a2enmod ssl
a2enmod rewrite

# Function to get SSL certificate
get_certificate() {
    local domain=$1
    echo "Getting certificate for $domain..."
    
    # Check if certificate already exists
    if certbot certificates | grep -q "$domain"; then
        echo "Certificate for $domain already exists, skipping..."
        return 0
    fi
    
    # Get certificate
    certbot certonly --apache --non-interactive --agree-tos --email admin@levelercc.com \
        -d "$domain" || echo "Failed to get certificate for $domain"
}

# Get certificates for all domains
for domain in "${DOMAINS[@]}"; do
    get_certificate "$domain"
done

# Update Apache configs to use SSL and redirect HTTP to HTTPS
echo ""
echo "Updating Apache configurations..."

# Function to update virtual host for SSL
update_vhost_ssl() {
    local domain=$1
    local config_file="/etc/apache2/sites-available/${domain}.conf"
    
    if [ ! -f "$config_file" ]; then
        echo "Config file not found: $config_file"
        return 1
    fi
    
    # Check if SSL config already exists
    if [ -f "/etc/apache2/sites-available/${domain}-le-ssl.conf" ]; then
        echo "SSL config already exists for $domain"
        return 0
    fi
    
    # Create SSL virtual host
    cat > "/etc/apache2/sites-available/${domain}-le-ssl.conf" << EOF
<VirtualHost *:443>
    ServerName ${domain}
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/${domain}/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/${domain}/privkey.pem
    Include /etc/letsencrypt/options-ssl-apache.conf
    
    # Copy content from HTTP config
    $(grep -v "ServerName\|<VirtualHost\|</VirtualHost>" "$config_file")
</VirtualHost>
EOF
    
    # Update HTTP config to redirect to HTTPS
    if ! grep -q "Redirect permanent" "$config_file"; then
        sed -i "/<VirtualHost/a\\    Redirect permanent / https://${domain}/" "$config_file"
    fi
    
    # Enable SSL site
    a2ensite "${domain}-le-ssl.conf" 2>/dev/null
}

# Update all domain configs
for domain in "${DOMAINS[@]}"; do
    # Get base domain (remove www.)
    base_domain=$(echo "$domain" | sed 's/^www\.//')
    
    # Find config file
    if [ -f "/etc/apache2/sites-available/${base_domain}.conf" ]; then
        update_vhost_ssl "$base_domain"
    elif [ -f "/etc/apache2/sites-available/${domain}.conf" ]; then
        update_vhost_ssl "$domain"
    fi
done

# Reload Apache
systemctl reload apache2

echo ""
echo "========================================="
echo "SSL Setup Complete!"
echo "========================================="
echo "All domains should now be accessible via HTTPS"
echo "HTTP requests will automatically redirect to HTTPS"
