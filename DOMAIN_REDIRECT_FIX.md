# Fix: levelercc.com Redirecting to biggestlogs.com

## Problem
The domain `levelercc.com` is redirecting to `biggestlogs.com` instead of showing the Leveler application.

## Possible Causes

1. **DNS pointing to wrong server** - DNS records may be pointing to a server that hosts biggestlogs.com
2. **Default virtual host** - The web server's default site may be catching the request
3. **Domain registrar redirect** - The domain registrar may have a redirect configured
4. **Wrong virtual host active** - Another virtual host may be configured for this IP

## Quick Fix (Run on Server)

```bash
cd /var/www/leveler
git pull origin main
chmod +x fix-domain-redirect.sh
sudo ./fix-domain-redirect.sh
```

## Manual Fix Steps

### Step 1: Check DNS Records

Verify DNS is pointing to the correct server:
```bash
nslookup levelercc.com
# Should return: 75.119.139.18
```

If it returns a different IP, update your DNS records at your domain registrar.

### Step 2: Disable Default Virtual Host

**For Apache:**
```bash
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

**For Nginx:**
```bash
sudo rm /etc/nginx/sites-enabled/default
sudo systemctl reload nginx
```

### Step 3: Verify Leveler Virtual Host is Active

**For Apache:**
```bash
# Check if leveler.conf is enabled
ls -la /etc/apache2/sites-enabled/ | grep leveler

# If not, enable it
sudo a2ensite leveler.conf
sudo systemctl reload apache2
```

**For Nginx:**
```bash
# Check if leveler is enabled
ls -la /etc/nginx/sites-enabled/ | grep leveler

# If not, enable it
sudo ln -s /etc/nginx/sites-available/leveler /etc/nginx/sites-enabled/
sudo systemctl reload nginx
```

### Step 4: Check for Other Virtual Hosts

**For Apache:**
```bash
# List all enabled sites
ls -la /etc/apache2/sites-enabled/

# Check each for biggestlogs or redirects
grep -r "biggestlogs\|Redirect\|RewriteRule" /etc/apache2/sites-enabled/
```

**For Nginx:**
```bash
# List all enabled sites
ls -la /etc/nginx/sites-enabled/

# Check each for biggestlogs or redirects
grep -r "biggestlogs\|return.*301\|rewrite" /etc/nginx/sites-enabled/
```

### Step 5: Verify Server Configuration

**For Apache:**
```bash
# Check the leveler.conf file
cat /etc/apache2/sites-available/leveler.conf | grep -i "ServerName\|ServerAlias"

# Should show:
# ServerName levelercc.com
# ServerAlias www.levelercc.com
```

**For Nginx:**
```bash
# Check the leveler file
cat /etc/nginx/sites-available/leveler | grep -i "server_name"

# Should show:
# server_name levelercc.com www.levelercc.com;
```

### Step 6: Test Locally

```bash
# Test with curl
curl -I http://levelercc.com
curl -I http://localhost

# Check what server is responding
curl -v http://levelercc.com 2>&1 | grep -i "server\|location"
```

### Step 7: Check Domain Registrar

1. Log into your domain registrar (where you bought levelercc.com)
2. Check for any URL forwarding or redirects configured
3. Remove any redirects to biggestlogs.com
4. Verify DNS A records point to: **75.119.139.18**

## Common Issues

### Issue: DNS still pointing to old server
**Solution:** Update DNS A records at your domain registrar to point to 75.119.139.18

### Issue: Default site still active
**Solution:** Disable the default site (see Step 2 above)

### Issue: Multiple virtual hosts conflicting
**Solution:** Ensure only the leveler virtual host is enabled for port 80

### Issue: Domain registrar has redirect
**Solution:** Remove any URL forwarding/redirects in your domain registrar's control panel

## Verification

After applying fixes, verify:

1. **DNS Resolution:**
   ```bash
   nslookup levelercc.com
   # Should return: 75.119.139.18
   ```

2. **Web Server Response:**
   ```bash
   curl -I http://levelercc.com
   # Should return HTTP 200, not a redirect
   ```

3. **Browser Test:**
   - Visit: http://levelercc.com
   - Should show Leveler homepage, not biggestlogs.com

## Still Not Working?

If the issue persists:

1. **Check server logs:**
   ```bash
   # Apache
   sudo tail -f /var/log/apache2/access.log
   sudo tail -f /var/log/apache2/error.log
   
   # Nginx
   sudo tail -f /var/log/nginx/access.log
   sudo tail -f /var/log/nginx/error.log
   ```

2. **Check if request is reaching the server:**
   ```bash
   # Monitor incoming requests
   sudo tcpdump -i any port 80 -n
   ```

3. **Verify IP binding:**
   ```bash
   # Check what's listening on port 80
   sudo netstat -tlnp | grep :80
   sudo ss -tlnp | grep :80
   ```

