# Email Delivery Issue - Gmail Bouncing Emails

## Problem
Emails are being sent successfully from Laravel, but Gmail is rejecting them with error:
```
550-5.7.26 Your email has been blocked because the sender is unauthenticated.
Gmail requires all senders to authenticate with either SPF or DKIM.
DKIM = did not pass
SPF [levelercc.com] with ip: [75.119.139.18] = did not pass
```

## Root Cause
The emails are being sent correctly, but Gmail requires proper email authentication:
1. **SPF (Sender Policy Framework)** - DNS record that authorizes which IPs can send emails for your domain
2. **DKIM (DomainKeys Identified Mail)** - Cryptographic signature that verifies the email came from your domain

## Current Status
- ✅ Laravel is sending emails successfully
- ✅ SMTP connection to Postfix is working
- ✅ Postfix is accepting emails
- ❌ Gmail is rejecting emails due to missing SPF/DKIM authentication

## Solution
Follow the instructions in `FIX_SPAM_ISSUES.md` to:
1. **Fix SPF Record** - Add/update SPF TXT record for `levelercc.com` to include your server IP `75.119.139.18`
2. **Add DKIM Record** - Add DKIM TXT record for `default._domainkey.levelercc.com`
3. **Update DMARC** - Ensure DMARC policy is configured correctly

## DNS Records Needed

### SPF Record
```
Type: TXT
Name: levelercc.com
Value: v=spf1 mx a:mail.levelercc.com ip4:75.119.139.18 ~all
TTL: 3600
```

### DKIM Record
Get your DKIM public key:
```bash
cat /etc/opendkim/keys/levelercc.com/default.txt
```

Then add as TXT record:
```
Type: TXT
Name: default._domainkey.levelercc.com
Value: (paste the entire content from the file)
TTL: 3600
```

### DMARC Record
```
Type: TXT
Name: _dmarc.levelercc.com
Value: v=DMARC1; p=none; rua=mailto:admin@levelercc.com; ruf=mailto:admin@levelercc.com; sp=none; pct=100;
TTL: 3600
```

## Verification
After adding DNS records, wait 24-48 hours for propagation, then test:
```bash
# Check SPF
dig TXT levelercc.com | grep spf

# Check DMARC
dig TXT _dmarc.levelercc.com

# Check DKIM
dig TXT default._domainkey.levelercc.com

# Test email delivery
# Send a test email to Gmail and check if it arrives
```

## Testing Tools
- **MXToolbox**: https://mxtoolbox.com/spf.aspx
- **Mail-Tester**: https://www.mail-tester.com/
- **Google Postmaster Tools**: https://postmaster.google.com/

## Timeline
- DNS changes: 24-48 hours to propagate
- Email deliverability improvement: 1-2 weeks after DNS is correct
- Full reputation building: 1-2 months

## Temporary Workaround
For testing purposes, you can:
1. Send emails to non-Gmail addresses (they may accept emails without SPF/DKIM)
2. Check Postfix logs: `/var/log/mail.log` to see delivery status
3. Use a transactional email service (SendGrid, Mailgun, etc.) as a temporary solution
