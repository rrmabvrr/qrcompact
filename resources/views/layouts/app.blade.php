<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'QRCompact' }}</title>
    <meta name="description" content="QRCompact: encurtador de links, QR Code e Pix.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Space+Grotesk:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body data-page="{{ $page ?? 'links' }}">

    <header class="app-header">
        <div class="inner">
            <a href="{{ route('links.index') }}" class="brand">
                <i class="bi bi-qr-code brand-icon" aria-hidden="true"></i>
                <span class="brand-name-wrap">
                    <span>QRCompact</span>
                    <span class="brand-beta">Beta</span>
                </span>
            </a>

            <nav class="header-nav" aria-label="Navegação principal">
                <a href="{{ route('links.index') }}" class="{{ ($page ?? '') === 'links' ? 'nav-active' : '' }}">Links
                    Curtos</a>
                <a href="{{ route('pix.index') }}" class="{{ ($page ?? '') === 'pix' ? 'nav-active' : '' }}">Gerar
                    Pix</a>
            </nav>

            <div class="header-actions">
                <a href="#" class="btn-header-ghost">Entrar</a>
                <a href="#" class="btn-premium">Conheça a versão premium!</a>
            </div>
        </div>
    </header>

    <main class="app-main">
        @yield('content')
    </main>

</body>

</html>