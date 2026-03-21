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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body data-page="{{ $page ?? 'links' }}">

    <header class="app-header">
        <div class="inner">
            <a href="{{ route('links.index') }}" class="brand">
                <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="5" y="5" width="3" height="3" fill="currentColor" stroke="none" />
                    <rect x="16" y="5" width="3" height="3" fill="currentColor" stroke="none" />
                    <rect x="5" y="16" width="3" height="3" fill="currentColor" stroke="none" />
                    <path d="M14 14h2v2h-2zM18 14h3v2h-3zM14 18h2v3h-2zM18 18h3v3h-3z" fill="currentColor"
                        stroke="none" />
                </svg>
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