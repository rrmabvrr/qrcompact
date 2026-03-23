<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Acesso - QRCompact' }}</title>
    <meta name="description" content="Acesso via codigo por email no QRCompact.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Space+Grotesk:wght@400;500;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="guest-body">
    <main class="guest-main">
        <section class="guest-card">
            <a href="{{ route('login') }}" class="guest-brand">
                <i class="bi bi-qr-code" aria-hidden="true"></i>
                <span>QRCompact</span>
            </a>

            @yield('content')
        </section>
    </main>
</body>

</html>
