<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends BaseResetPassword
{
    use Queueable;

    /**
     * Build the mail representation in pt-BR.
     */
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);
        $expire = Config::get('auth.passwords.' . Config::get('auth.defaults.passwords') . '.expire');

        return (new MailMessage)
            ->subject('Recuperação de senha - QRCompact')
            ->line('Você está recebendo este e-mail porque solicitou a redefinição da senha da sua conta.')
            ->action('Redefinir senha', $resetUrl)
            ->line(Lang::get('Este link de redefinição de senha expira em :count minutos.', ['count' => $expire]))
            ->line('Se você não solicitou a redefinição de senha, nenhuma ação adicional é necessária.');
    }
}
