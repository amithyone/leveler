# Mail Server DNS Configuration

## Server IP: 75.119.139.18

---

## For levelercc.com

### 1. MX Record (Mail Exchange)
```
Type: MX
Name: @ (or levelercc.com)
Priority: 10
Value: mail.levelercc.com
TTL: 3600
```

### 2. A Record for Mail Server
```
Type: A
Name: mail
Value: 75.119.139.18
TTL: 3600
```

### 3. SPF Record (Sender Policy Framework)
```
Type: TXT
Name: @ (or levelercc.com)
Value: v=spf1 mx a:mail.levelercc.com ip4:75.119.139.18 ~all
TTL: 3600
```

### 4. DKIM Record (DomainKeys Identified Mail)
```
Type: TXT
Name: default._domainkey
Value: [See DKIM key below]
TTL: 3600
```

### 5. DMARC Record (Domain-based Message Authentication)
```
Type: TXT
Name: _dmarc
Value: v=DMARC1; p=quarantine; rua=mailto:admin@levelercc.com; ruf=mailto:admin@levelercc.com; sp=quarantine; aspf=r;
TTL: 3600
```

---

## For biggestlogs.com

### 1. MX Record (Mail Exchange)
```
Type: MX
Name: @ (or biggestlogs.com)
Priority: 10
Value: mail.biggestlogs.com
TTL: 3600
```

### 2. A Record for Mail Server
```
Type: A
Name: mail
Value: 75.119.139.18
TTL: 3600
```

### 3. SPF Record (Sender Policy Framework)
```
Type: TXT
Name: @ (or biggestlogs.com)
Value: v=spf1 mx a:mail.biggestlogs.com ip4:75.119.139.18 ~all
TTL: 3600
```

### 4. DKIM Record (DomainKeys Identified Mail)
```
Type: TXT
Name: default._domainkey
Value: [See DKIM key below]
TTL: 3600
```

### 5. DMARC Record (Domain-based Message Authentication)
```
Type: TXT
Name: _dmarc
Value: v=DMARC1; p=quarantine; rua=mailto:admin@biggestlogs.com; ruf=mailto:admin@biggestlogs.com; sp=quarantine; aspf=r;
TTL: 3600
```

---

## Additional Records

### Webmin Subdomain (Optional)
```
Type: A
Name: webmin
Value: 75.119.139.18
TTL: 3600
```

---

## Notes:
- Replace DKIM values below with actual keys from server
- Wait 5-30 minutes for DNS propagation after adding records
- Verify DNS records with: `dig MX levelercc.com` or `nslookup -type=MX levelercc.com`
- Test SPF: `dig TXT levelercc.com`
- Test DKIM: `dig TXT default._domainkey.levelercc.com`
- Test DMARC: `dig TXT _dmarc.levelercc.com`
