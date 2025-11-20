# Fix: levelercc.com Redirecting to biggestlogs.com

## Problem
The domain `levelercc.com` is redirecting to `biggestlogs.com` instead of showing the Leveler application.

## Possible Causes

1. **DNS pointing to wrong server** - DNS records may be pointing to a server that hosts biggestlogs.com
2. **Default web server configuration** - The server might have a default site that catches all requests
3. **Domain registrar redirect** - Some registrars have domain parking or redirect features enabled
4. **Multiple virtual hosts** - Another virtual host might be catching the domain first
5. **Browser cache** - Your browser might be caching the old redirect

## Quick Fix (Run on Server)

```bash
cd /var/www/leveler
git pull origin main
chmod +x fix-domain-redirect.sh
sudo ./fix-domain-redirect.sh
```

## Manual Fix Steps

### Step 1: Verify DNS Records

Check where your domain is pointing:

```bash
nslookup levelercc.com
host levelercc.com
dig levelercc.com
```

**Should show:** `75.119.139.18`

If it shows a different IP, update your DNS records at your domain registrar:
- Type: A
- Name: @ (or blank)
- Value: 75.119.139.18
- TTL: 3600

### Step 2: Check Web Server Configuration

#### For Apache:

```bash
# Check all enabled sites
ls -la /etc/apache2/sites-enabled/

# Check for any sites with biggestlogs
grep -r "biggestlogs" /etc/apache2/sites-enabled/

# Check for redirects
grep -r "Redirect\|RewriteRule" /etc/apache2/sites-enabled/

# Ensure leveler.conf is enabled and default is disabled
sudo a2ensite leveler.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

#### For Nginx:

```bash
# Check all enabled sites
ls -la /etc/nginx/sites-enabled/

# Check for any sites with biggestlogs
grep -r "biggestlogs" /etc/nginx/sites-enabled/

# Check for redirects
grep -r "return.*301\|rewrite" /etc/nginx/sites-enabled/

# Ensure leveler is enabled and default is removed
sudo ln -sf /etc/nginx/sites-available/leveler /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

### Step 3: Check Domain Registrar

1. Log into your domain registrar (where you bought levelercc.com)
2. Look for:
   - "Domain Parking" settings
   - "Domain Redirect" settings
   - "Nameservers" - should point to your hosting provider's nameservers
3. Disable any redirects or parking features
4. Ensure nameservers are correct

### Step 4: Verify Server Configuration

Ensure the leveler site configuration is correct:

```bash
# For Apache
cat /etc/apache2/sites-available/leveler.conf | grep -E "ServerName|DocumentRoot"

# For Nginx
cat /etc/nginx/sites-available/leveler | grep -E "server_name|root"
```

Should show:
- ServerName: `levelercc.com`
- DocumentRoot: `/var/www/leveler/public`

### Step 5: Test Locally

Test if the server responds correctly:

```bash
# Test with curl
curl -I http://localhost -H "Host: levelercc.com"

# Should return 200 OK, not a redirect
```

### Step 6: Check for Other Virtual Hosts

Make sure no other virtual host is catching levelercc.com:

#### Apache:
```bash
# Check each for biggestlogs or redirects
grep -r "biggestlogs\|Redirect\|RewriteRule" /etc/apache2/sites-enabled/
```

#### Nginx:
```bash
# Check each for biggestlogs or redirects
grep -r "biggestlogs\|return.*301\|rewrite" /etc/nginx/sites-enabled/
```

### Step 7: Clear Browser Cache

1. Clear your browser cache
2. Try incognito/private mode
3. Try a different browser
4. Try from a different network/device

## Verification

After fixing, test:

1. **DNS Check:**
   ```bash
   nslookup levelercc.com
   # Should return: 75.119.139.18
   ```

2. **Web Server Test:**
   ```bash
   curl -I http://levelercc.com
   # Should return: HTTP/1.1 200 OK (not 301/302 redirect)
   ```

3. **Browser Test:**
   - Visit: `http://levelercc.com`
   - Should show: Leveler homepage (not biggestlogs.com)

## Common Issues

### Issue: DNS points to wrong IP
**Solution:** Update DNS A record at registrar to point to `75.119.139.18`

### Issue: Default site catching all requests
**Solution:** Disable default site, ensure leveler.conf has priority

### Issue: Domain registrar has redirect enabled
**Solution:** Log into registrar and disable redirect/parking

### Issue: Multiple virtual hosts with same ServerName
**Solution:** Remove or disable conflicting virtual hosts

## Still Not Working?

If the issue persists:

1. Check web server error logs:
   ```bash
   # Apache
   tail -f /var/log/apache2/error.log
   
   # Nginx
   tail -f /var/log/nginx/error.log
   ```

2. Check access logs:
   ```bash
   # Apache
   tail -f /var/log/apache2/access.log
   
   # Nginx
   tail -f /var/log/nginx/access.log
   ```

3. Test with different tools:
   ```bash
   # Using curl
   curl -v http://levelercc.com
   
   # Using wget
   wget --spider -S http://levelercc.com 2>&1 | grep -E "HTTP|Location"
   ```

4. Contact your hosting provider if DNS is correct but server isn't responding
