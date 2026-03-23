<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public int $expiresInMinutes,
        public bool $isFirstAccess = false,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->isFirstAccess
            ? 'Confirme seu email para acessar o QRCompact'
            : 'Seu codigo de acesso - QRCompact';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.login-code',
            with: [
                'code' => $this->code,
                'expiresInMinutes' => $this->expiresInMinutes,
                'isFirstAccess' => $this->isFirstAccess,
            ],
        );
    }
}
