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
                <a href="{{ route('whatsapp.index') }}"
                    class="{{ ($page ?? '') === 'whatsapp' ? 'nav-active' : '' }}">WhatsApp</a>
            </nav>

            <div class="header-actions">
                <a href="#" class="btn-header-ghost">Entrar</a>
                <a href="#" class="btn-premium">Conheça a versão premium!</a>
            </div>
        </div>
    </header>

    <main class="app-main">
        <div class="page-grid">
            <div>
                <div class="qrc-card">
                    <h2 class="section-heading">Selecione o tipo de conteúdo</h2>

                    <div class="type-toggle">
                        <button type="button" class="type-toggle-btn active">
                            <i class="bi bi-patch-question" aria-hidden="true"></i>
                            Básico
                        </button>
                        <button type="button" class="type-toggle-btn">
                            <i class="bi bi-stars" aria-hidden="true"></i>
                            Premium
                        </button>
                    </div>

                    <div class="content-type-grid">
                        <a class="type-card {{ ($page ?? '') === 'links' ? 'active' : '' }}"
                            href="{{ route('links.index') }}">
                            <span class="type-card-icon"><i class="bi bi-link-45deg" aria-hidden="true"></i></span>
                            Link Único
                        </a>
                        <a class="type-card {{ ($page ?? '') === 'pix' ? 'active' : '' }}"
                            href="{{ route('pix.index') }}">
                            <span class="type-card-icon"><i class="bi bi-currency-exchange"
                                    aria-hidden="true"></i></span>
                            Pix
                        </a>
                        <a class="type-card {{ ($page ?? '') === 'whatsapp' ? 'active' : '' }}"
                            href="{{ route('whatsapp.index') }}">
                            <span class="type-card-icon"><i class="bi bi-whatsapp" aria-hidden="true"></i></span>
                            WhatsApp
                        </a>
                        <span class="type-card"><span class="type-card-icon"><i class="bi bi-wifi"
                                    aria-hidden="true"></i></span>Wi-Fi</span>
                        <span class="type-card"><span class="type-card-icon"><i class="bi bi-card-text"
                                    aria-hidden="true"></i></span>Texto</span>
                        <span class="type-card"><span class="type-card-icon"><i class="bi bi-telephone"
                                    aria-hidden="true"></i></span>Chamada</span>
                        <span class="type-card"><span class="type-card-icon"><i class="bi bi-envelope"
                                    aria-hidden="true"></i></span>Email</span>
                        <span class="type-card"><span class="type-card-icon"><i class="bi bi-chat-left-text"
                                    aria-hidden="true"></i></span>SMS</span>
                    </div>

                    <h3 class="content-label">Conteúdo</h3>
                    @yield('form-content')
                </div>

                @yield('left-column-extra')
            </div>

            <div>
                <div class="qr-preview-panel">
                    <div class="qr-samples-grid" data-qr-placeholder>
                        <div class="qr-sample-box">
                            <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg" width="72" height="72">
                                <rect x="4" y="4" width="30" height="30" rx="3" fill="#b8c5d6" />
                                <rect x="10" y="10" width="18" height="18" fill="#dde4ef" rx="1.5" />
                                <rect x="46" y="4" width="30" height="30" rx="3" fill="#b8c5d6" />
                                <rect x="52" y="10" width="18" height="18" fill="#dde4ef" rx="1.5" />
                                <rect x="4" y="46" width="30" height="30" rx="3" fill="#b8c5d6" />
                                <rect x="10" y="52" width="18" height="18" fill="#dde4ef" rx="1.5" />
                                <rect x="46" y="46" width="8" height="8" fill="#b8c5d6" />
                                <rect x="58" y="46" width="8" height="8" fill="#b8c5d6" />
                                <rect x="46" y="58" width="8" height="8" fill="#b8c5d6" />
                                <rect x="70" y="58" width="6" height="6" fill="#b8c5d6" />
                                <rect x="58" y="70" width="8" height="6" fill="#b8c5d6" />
                            </svg>
                        </div>
                    </div>

                    @yield('qr-result-content')

                    <button type="submit" form="@yield('generate-form-id')" class="btn-generate">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                        @yield('generate-label', 'Gerar QRCode')
                    </button>
                </div>
            </div>
        </div>

        @yield('content')
    </main>

</body>

</html>