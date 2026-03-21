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
    <body data-page="{{ $page ?? 'links' }}" class="app-body">
        <header class="py-3">
            <div class="container-xxl">
                <nav class="navbar navbar-expand-md navbar-surface px-3 px-lg-4 py-3 rounded-4">
                    <a href="{{ route('links.index') }}" class="navbar-brand d-flex align-items-center gap-3 fw-semibold mb-0">
                        <span class="brand-mark">Q</span>
                        <span>QRCompact</span>
                    </a>

                    <div class="ms-md-auto d-flex align-items-center gap-2 nav nav-pills flex-nowrap" aria-label="Navegacao principal">
                        <a href="{{ route('links.index') }}" class="nav-link rounded-pill px-3 px-lg-4 {{ ($page ?? '') === 'links' ? 'active' : '' }}">Links Curtos</a>
                        <a href="{{ route('pix.index') }}" class="nav-link rounded-pill px-3 px-lg-4 {{ ($page ?? '') === 'pix' ? 'active' : '' }}">Gerar Pix</a>
                    </div>
                </nav>
            </div>
        </header>

        <main class="container-xxl pb-5">
            @yield('content')
        </main>
    </body>
</html>