# DNS Records That Need to Be Added

## Current Status
✅ **Server is working correctly:**
- OpenDKIM is signing emails
- Postfix is sending emails
- Emails are being rejected by Gmail because DNS records are missing

## Required DNS Records

### 1. SPF Record (CRITICAL - Add This First)
**Where to add:** Your DNS provider (where you manage levelercc.com DNS)  
**Type:** TXT  
**Name/Host:** `levelercc.com` (or `@`)  
**Value:** `v=spf1 mx a:mail.levelercc.com ip4:75.119.139.18 ~all`  
**TTL:** 3600

### 2. DKIM Record (CRITICAL)
**Where to add:** Your DNS provider  
**Type:** TXT  
**Name/Host:** `default._domainkey.levelercc.com`  
**Value:** (Copy the entire value below - it's one long string)
```
v=DKIM1; h=sha256; k=rsa; p=MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAx0rvbV0HB8F2zIO4zqTtycZVSHaYLXq8SWgYPBKetFt6eNDbSfcsaI9XUKxPp4kKdXgrJLWVWQR6vtodkr0tBAiSsYTlDAjPXepfh0kjDpaai53gGdOLMkROZAuxnVRuCXt6iBTWUrnRIvd8o9UJNfMOamHZzIW1ppS8CH8CHtclSqBEzi0jUMtWCyG48C8en4Aue173kJ6pWE3C5J18KVFkNLMGqeHAGi5JUrDpomlt248etXCawC2h99PkDROwYvlMOMAAWKapa9CYbQwNhm77CtyU6L0CVVoQkoB5KUkGi9UFZSxRZGeC1BMIirHv1nvArVK+jM3JQB3q6ZtVfwIDAQAB
```

**Important:** This must be ONE continuous string with NO line breaks or spaces between the parts.

### 3. DMARC Record (RECOMMENDED)
**Where to add:** Your DNS provider  
**Type:** TXT  
**Name/Host:** `_dmarc.levelercc.com`  
**Value:** `v=DMARC1; p=none; rua=mailto:admin@levelercc.com; ruf=mailto:admin@levelercc.com; sp=none; pct=100;`  
**TTL:** 3600

## Step-by-Step Instructions

1. **Log into your DNS provider** (where you manage levelercc.com DNS records)
2. **Add SPF record first** - This is the most critical
3. **Add DKIM record** - Make sure it's exactly as shown above (one continuous string)
4. **Add DMARC record** - For monitoring and reputation
5. **Wait 15-60 minutes** for DNS propagation
6. **Verify** using the commands below

## Verification Commands

After adding DNS records, run these on the server to verify:

```bash
# Check SPF
dig TXT levelercc.com +short

# Check DKIM  
dig TXT default._domainkey.levelercc.com +short

# Check DMARC
dig TXT _dmarc.levelercc.com +short
```

You should see the values you added. If you see nothing, DNS hasn't propagated yet (wait longer) or the records weren't added correctly.

## Why Emails Aren't Working

The server is working perfectly:
- ✅ Emails are being sent
- ✅ Emails are being signed with DKIM
- ❌ Gmail rejects them because DNS records don't exist to verify the signatures

Once DNS records are added and propagated, Gmail will be able to verify:
- SPF: That your IP (75.119.139.18) is authorized to send for levelercc.com
- DKIM: That the email signature matches the public key in DNS

## Timeline

- **DNS propagation:** 15 minutes to 48 hours (usually 15-60 minutes)
- **After DNS is live:** Emails should work immediately
- **Full reputation:** 1-2 weeks of good delivery

## Testing

After DNS records are added and propagated:
1. Send a test email
2. Check Gmail headers (Show original)
3. Look for: `dkim=pass` and `spf=pass` in Authentication-Results header
