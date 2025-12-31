<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\MailManager;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure SMTP stream options after mail manager is resolved
        $this->app->afterResolving('mail.manager', function (MailManager $manager) {
            $manager->extend('smtp', function ($config) use ($manager) {
                // Create transport using the manager's method
                $transport = $manager->createSymfonyTransport($config);
                
                // Configure stream options if it's an EsmtpTransport
                if ($transport instanceof EsmtpTransport) {
                    $stream = $transport->getStream();
                    
                    if ($stream instanceof SocketStream) {
                        // Get existing stream options
                        $streamOptions = $stream->getStreamOptions();
                        
                        // Add SSL options to disable certificate verification for local mail server
                        $streamOptions['ssl'] = array_merge($streamOptions['ssl'] ?? [], [
                            'allow_self_signed' => true,
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ]);
                        
                        // Set the updated stream options
                        $stream->setStreamOptions($streamOptions);
                    }
                }
                
                return $transport;
            });
        });
    }
}
