<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
            // Store original createSmtpTransport method
            $originalCreateSmtp = \Closure::bind(function ($config) {
                $factory = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
                
                $scheme = $config['scheme'] ?? null;
                if (!$scheme) {
                    $scheme = !empty($config['encryption']) && $config['encryption'] === 'tls'
                        ? (($config['port'] == 465) ? 'smtps' : 'smtp')
                        : '';
                }
                
                $transport = $factory->create(new \Symfony\Component\Mailer\Transport\Dsn(
                    $scheme,
                    $config['host'],
                    $config['username'] ?? null,
                    $config['password'] ?? null,
                    $config['port'] ?? null,
                    $config
                ));
                
                // Configure stream options
                $stream = $transport->getStream();
                if ($stream instanceof SocketStream) {
                    if (isset($config['source_ip'])) {
                        $stream->setSourceIp($config['source_ip']);
                    }
                    if (isset($config['timeout'])) {
                        $stream->setTimeout($config['timeout']);
                    }
                    
                    // Add SSL options to disable certificate verification for local mail server
                    $streamOptions = $stream->getStreamOptions();
                    $streamOptions['ssl'] = array_merge($streamOptions['ssl'] ?? [], [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ]);
                    $stream->setStreamOptions($streamOptions);
                }
                
                return $transport;
            }, $manager, MailManager::class);
            
            // Replace the createSmtpTransport method
            $manager->extend('smtp', function ($config) use ($originalCreateSmtp) {
                return $originalCreateSmtp($config);
            });
        });
    }
}
