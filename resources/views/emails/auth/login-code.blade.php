<!DOCTYPE html>
<html lang="pt-BR">

<body style="font-family: Arial, sans-serif; color: #1a2233; line-height: 1.6; max-width: 480px; margin: 0 auto; padding: 24px;">
    @if ($isFirstAccess)
        <p>Bem-vindo ao <strong>QRCompact</strong>! Use o codigo abaixo para confirmar seu email e ativar sua conta:</p>
    @else
        <p>Use o codigo abaixo para acessar o <strong>QRCompact</strong>:</p>
    @endif

    <p style="font-size: 32px; letter-spacing: 6px; font-weight: 700; margin: 20px 0; color: #e8456a;">
        {{ $code }}
    </p>

    <p style="color: #6b7a96; font-size: 0.9rem;">
        Este codigo expira em {{ $expiresInMinutes }} minutos.<br>
        Se voce nao solicitou este email, pode ignora-lo com seguranca.
    </p>
</body>

</html>
