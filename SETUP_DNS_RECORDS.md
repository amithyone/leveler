# DNS Records Setup Guide for Email Delivery

## Current Status
- ✅ OpenDKIM is running and configured
- ✅ DKIM key exists: `/etc/opendkim/keys/levelercc.com/default.txt`
- ❌ DNS records are NOT found (not added or not propagated)
- ❌ Postfix may not be configured to sign emails with DKIM

## Required DNS Records

### 1. SPF Record (CRITICAL)
**Type:** TXT  
**Name:** `levelercc.com` (or `@`)  
**Value:** `v=spf1 mx a:mail.levelercc.com ip4:75.119.139.18 ~all`  
**TTL:** 3600

**Explanation:**
- `mx` - Authorizes mail servers listed in MX records
- `a:mail.levelercc.com` - Authorizes the mail subdomain
- `ip4:75.119.139.18` - Authorizes your server IP
- `~all` - Soft fail for other IPs (use `-all` for hard fail after testing)

### 2. DKIM Record (CRITICAL)
**Type:** TXT  
**Name:** `default._domainkey.levelercc.com`  
**Value:** (See below - get from server)  
**TTL:** 3600

**To get the DKIM value, run on server:**
```bash
cat /etc/opendkim/keys/levelercc.com/default.txt | grep -v '^;' | tr -d '\n' | sed 's/\" \"/\"\"/g'
```

The value should look like:
```
v=DKIM1; h=sha256; k=rsa; p=MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAx0rvbV0HB8F2zIO4zqTtycZVSHaYLXq8SWgYPBKetFt6eNDbSfcsaI9XUKxPp4kKdXgrJLWVWQR6vtodkr0tBAiSsYTlDAjPXepfh0kjDpaai53gGdOLMkROZAuxnVRuCXt6iBTWUrnRIvd8o9UJNfMOamHZzIW1ppS8CH8CHtclSqBEzi0jUMtWCyG48C8en4Aue173kJ6pWE3C5J18KVFkNLMGqeHAGi5JUrDpomlt248etXCawC2h99PkDROwYvlMOMAAWKapa9CYbQwNhm77CtyU6L0CVVoQkoB5KUkGi9UFZSxRZGeC1BMIirHv1nvArVK+jM3JQB3q6ZtVfwIDAQAB
```

### 3. DMARC Record (RECOMMENDED)
**Type:** TXT  
**Name:** `_dmarc.levelercc.com`  
**Value:** `v=DMARC1; p=none; rua=mailto:admin@levelercc.com; ruf=mailto:admin@levelercc.com; sp=none; pct=100;`  
**TTL:** 3600

**Explanation:**
- `p=none` - Start with no policy (monitoring only)
- After 1-2 weeks, change to `p=quarantine` or `p=reject`
- `rua` - Aggregate reports email
- `ruf` - Forensic reports email

## Step-by-Step Instructions

### Step 1: Add SPF Record
1. Log into your DNS provider (where you manage levelercc.com DNS)
2. Add a new TXT record:
   - **Name/Host:** `levelercc.com` or `@`
   - **Type:** `TXT`
   - **Value:** `v=spf1 mx a:mail.levelercc.com ip4:75.119.139.18 ~all`
   - **TTL:** `3600`

### Step 2: Add DKIM Record
1. Get the DKIM public key from the server (see command above)
2. Add a new TXT record:
   - **Name/Host:** `default._domainkey.levelercc.com`
   - **Type:** `TXT`
   - **Value:** (paste the entire DKIM key value from the server)
   - **TTL:** `3600`

### Step 3: Add DMARC Record
1. Add a new TXT record:
   - **Name/Host:** `_dmarc.levelercc.com`
   - **Type:** `TXT`
   - **Value:** `v=DMARC1; p=none; rua=mailto:admin@levelercc.com; ruf=mailto:admin@levelercc.com; sp=none; pct=100;`
   - **TTL:** `3600`

## Verification

After adding DNS records, wait 15-60 minutes, then verify:

```bash
# Check SPF
dig TXT levelercc.com +short | grep spf

# Check DKIM
dig TXT default._domainkey.levelercc.com +short

# Check DMARC
dig TXT _dmarc.levelercc.com +short

# Test DKIM signing
opendkim-testkey -d levelercc.com -s default -vvv
```

## Testing Email Delivery

1. Send a test email to Gmail
2. Check Gmail headers:
   - Open email → Click "Show original" (three dots menu)
   - Look for `Authentication-Results` header
   - Should show: `dkim=pass` and `spf=pass`

## Troubleshooting

### If emails still bounce:
1. **Wait longer** - DNS can take 24-48 hours to fully propagate
2. **Check DNS propagation** - Use https://dnschecker.org/
3. **Verify Postfix is signing** - Check `/var/log/mail.log` for DKIM signatures
4. **Check OpenDKIM logs** - `/var/log/mail.log | grep dkim`

### If DKIM test fails:
1. Ensure DNS record is exactly as shown (no extra spaces)
2. Ensure selector matches (`default`)
3. Check OpenDKIM is running: `systemctl status opendkim`
4. Verify Postfix is using milter: `postconf | grep milter`

## Next Steps After DNS is Set

1. Wait 24-48 hours for DNS propagation
2. Send test emails
3. Monitor DMARC reports
4. After 1-2 weeks, change DMARC policy from `p=none` to `p=quarantine`
