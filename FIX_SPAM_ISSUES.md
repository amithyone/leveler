# Fix Email Spam Issues

## Current DNS Records Status

### SPF Records
Currently you have **duplicate SPF records**, which is invalid. You need to consolidate them into a single SPF record.

**Current (INVALID - Multiple SPF records):**
```
levelercc.com.  TXT  "v=spf1 mx a:mail.levelercc.com ip4:75.119.139.18 ~all"
levelercc.com.  TXT  "v=spf1 a mx ip4:102.218.215.41 ~all"
```

**Recommended (Single SPF record):**
```
levelercc.com.  TXT  "v=spf1 mx a:mail.levelercc.com ip4:75.119.139.18 ip4:102.218.215.41 ~all"
```

### DMARC Record
Your DMARC is set to `quarantine`, which is good but might be too strict initially.

**Current:**
```
_dmarc.levelercc.com.  TXT  "v=DMARC1; p=quarantine; rua=mailto:admin@levelercc.com; sp=quarantine;"
```

**Recommended (Start with `none` then move to `quarantine` after monitoring):**
```
_dmarc.levelercc.com.  TXT  "v=DMARC1; p=none; rua=mailto:admin@levelercc.com; ruf=mailto:admin@levelercc.com; sp=none; pct=100;"
```

### DKIM Record
DKIM is configured and working. You need to add the DKIM public key to DNS.

**Check your DKIM public key:**
```bash
cat /etc/opendkim/keys/levelercc.com/default.txt
```

**Add this as a TXT record:**
```
default._domainkey.levelercc.com.  TXT  "v=DKIM1; k=rsa; p=YOUR_PUBLIC_KEY_HERE"
```

## Steps to Fix Spam Issues

### 1. Fix SPF Record (CRITICAL)
1. Log into your DNS provider
2. Delete ALL existing SPF TXT records for `levelercc.com`
3. Add a SINGLE SPF record:
   ```
   Type: TXT
   Name: levelercc.com
   Value: v=spf1 mx a:mail.levelercc.com ip4:75.119.139.18 ip4:102.218.215.41 ~all
   TTL: 3600
   ```

### 2. Update DMARC Policy
1. Update the DMARC record to start with `p=none`:
   ```
   Type: TXT
   Name: _dmarc.levelercc.com
   Value: v=DMARC1; p=none; rua=mailto:admin@levelercc.com; ruf=mailto:admin@levelercc.com; sp=none; pct=100;
   TTL: 3600
   ```
2. Monitor DMARC reports for 1-2 weeks
3. After confirming emails are working, change to `p=quarantine` or `p=reject`

### 3. Add DKIM Record
1. Get your DKIM public key:
   ```bash
   cat /etc/opendkim/keys/levelercc.com/default.txt
   ```
2. Add as TXT record:
   ```
   Type: TXT
   Name: default._domainkey.levelercc.com
   Value: (paste the entire content from the file, starting with "v=DKIM1; k=rsa; p=...")
   TTL: 3600
   ```

### 4. Verify Configuration
After making changes, verify with:
```bash
# Check SPF
dig TXT levelercc.com | grep spf

# Check DMARC
dig TXT _dmarc.levelercc.com

# Check DKIM
dig TXT default._domainkey.levelercc.com

# Test DKIM signing
opendkim-testkey -d levelercc.com -s default -vvv
```

### 5. Additional Recommendations

#### Reverse DNS (PTR Record)
Ensure your server IP has a reverse DNS record pointing to `mail.levelercc.com`:
- Contact your hosting provider to set up PTR record
- IP: 75.119.139.18 → mail.levelercc.com

#### Email Content Best Practices
- Avoid spam trigger words
- Don't use all caps
- Include unsubscribe links
- Use proper HTML formatting
- Don't send to invalid email addresses

#### Warm Up Your IP
- Start with low email volumes
- Gradually increase over time
- Monitor bounce rates

## Testing Tools

Use these tools to test your email configuration:
- **MXToolbox**: https://mxtoolbox.com/spf.aspx
- **Mail-Tester**: https://www.mail-tester.com/
- **DMARC Analyzer**: https://dmarcian.com/dmarc-xml/

## Expected Timeline

- DNS changes: 24-48 hours to propagate
- Reputation building: 2-4 weeks
- Full deliverability: 1-2 months
