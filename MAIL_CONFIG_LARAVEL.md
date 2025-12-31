# Laravel Mail Configuration for levelercc.com

After setting up the mail server, configure Laravel to use it.

## .env Configuration

Update your `.env` file with these settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.levelercc.com
MAIL_PORT=587
MAIL_USERNAME=noreply@levelercc.com
MAIL_PASSWORD=YOUR_EMAIL_PASSWORD_HERE
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@levelercc.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Alternative Ports

If port 587 doesn't work, try:
- **Port 465** with `MAIL_ENCRYPTION=ssl`
- **Port 25** with `MAIL_ENCRYPTION=null` (not recommended for production)

## Create Email Account for Laravel

Create a dedicated email account for sending system emails:

```bash
ssh root@75.119.139.18
cd /var/www/leveler
./create-email-account.sh noreply@levelercc.com "StrongPassword123!"
```

## Testing Email in Laravel

Create a test route or use tinker:

```php
// routes/web.php (temporary test route)
Route::get('/test-email', function () {
    Mail::raw('This is a test email from Leveler', function ($message) {
        $message->to('your-email@example.com')
                ->subject('Test Email from Leveler');
    });
    return 'Email sent!';
});
```

Or use Laravel Tinker:
```bash
php artisan tinker
```

```php
Mail::raw('Test email', function ($message) {
    $message->to('your-email@example.com')
            ->subject('Test');
});
```

## Common Issues

### Connection Timeout
- Check firewall: `ufw status`
- Verify port is open: `telnet mail.levelercc.com 587`
- Check Postfix is running: `systemctl status postfix`

### Authentication Failed
- Verify username/password in `.env`
- Check email account exists: `mysql mailserver -e "SELECT email FROM virtual_users;"`
- Test login manually: `telnet mail.levelercc.com 587`

### Emails Not Sending
- Check mail logs: `tail -f /var/log/mail.log`
- Check mail queue: `postqueue -p`
- Verify DNS records (MX, SPF, DKIM)

## Production Recommendations

1. **Use dedicated email account** for system emails (noreply@levelercc.com)
2. **Set up email queue** for better performance:
   ```env
   QUEUE_CONNECTION=database
   ```
3. **Monitor email logs** regularly
4. **Set up email templates** for consistent branding
5. **Test email delivery** to major providers (Gmail, Outlook, etc.)

## Email Templates Example

```php
// app/Mail/WelcomeEmail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Welcome to Leveler')
                    ->view('emails.welcome');
    }
}
```

## Queue Configuration

For better performance, use queues:

```bash
php artisan queue:table
php artisan migrate
```

Update `.env`:
```env
QUEUE_CONNECTION=database
```

Run queue worker:
```bash
php artisan queue:work
```

Or use supervisor for production.

