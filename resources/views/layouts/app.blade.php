<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'QRCompact' }}</title>
        <meta name="description" content="QRCompact: encurtador de links, QR Code e Pix em Laravel 12.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body data-page="{{ $page ?? 'links' }}">
        <header class="topbar">
            <a href="{{ route('links.index') }}" class="brand">
                <span class="brand__mark">Q</span>
                <span>QRCompact</span>
            </a>

            <nav class="nav" aria-label="Navegacao principal">
                <a href="{{ route('links.index') }}" class="nav__link {{ ($page ?? '') === 'links' ? 'is-active' : '' }}">Links Curtos</a>
                <a href="{{ route('pix.index') }}" class="nav__link {{ ($page ?? '') === 'pix' ? 'is-active' : '' }}">Gerar Pix</a>
            </nav>
        </header>

        <main class="page-shell">
            @yield('content')
        </main>
    </body>
</html>