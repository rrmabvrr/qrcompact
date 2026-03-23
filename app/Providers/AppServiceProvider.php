<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Mail::extend('smtp', function (array $config) {
            $isTls = in_array($config['scheme'] ?? '', ['smtps', 'ssl'], true)
                || (int) ($config['port'] ?? 465) === 465;

            $transport = new EsmtpTransport(
                $config['host'] ?? 'localhost',
                (int) ($config['port'] ?? 465),
                $isTls,
            );

            $stream = $transport->getStream();
            if ($stream instanceof SocketStream) {
                $stream->setStreamOptions([
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                    ],
                ]);
            }

            if (! empty($config['username'])) {
                $transport->setUsername($config['username']);
            }

            if (! empty($config['password'])) {
                $transport->setPassword($config['password']);
            }

            return $transport;
        });
    }
}
