<!DOCTYPE html>
<html lang="pt-BR">

<body style="font-family: Arial, sans-serif; color: #1a2233; line-height: 1.5;">
    <p>Seu codigo de acesso ao <strong>QRCompact</strong>:</p>

    <p style="font-size: 28px; letter-spacing: 4px; font-weight: 700; margin: 16px 0;">
        {{ $code }}
    </p>

    <p>Este codigo expira em {{ $expiresInMinutes }} minutos.</p>
    <p>Se voce nao solicitou este acesso, ignore este email.</p>
</body>

</html>
