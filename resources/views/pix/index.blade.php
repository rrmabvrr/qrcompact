@extends('layouts.app', ['title' => 'Gerar QR Code Pix', 'page' => 'pix'])

@section('content')
<div class="page-grid">

    {{-- Left column --}}
    <div>
        <div class="qrc-card">
            <h2 class="section-heading">Selecione o tipo de conteúdo</h2>

            <div class="type-toggle">
                <button type="button" class="type-toggle-btn active">
                    <svg viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                        <path
                            d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                    </svg>
                    Básico
                </button>
                <button type="button" class="type-toggle-btn">
                    <svg viewBox="0 0 16 16" fill="currentColor">
                        <path
                            d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                    </svg>
                    Premium
                </button>
            </div>

            <div class="content-type-grid">
                <a class="type-card" href="{{ route('links.index') }}">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                        </svg>
                    </span>
                    Link Único
                </a>
                <a class="type-card active" href="{{ route('pix.index') }}">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <path d="M2 10h20" />
                        </svg>
                    </span>
                    Pix
                </a>
                <span class="type-card">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12 19.79 19.79 0 0 1 1.93 3.38 2 2 0 0 1 3.9 1.2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                    </span>
                    Chamada
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                    </span>
                    Email
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </span>
                    V-Card
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                        </svg>
                    </span>
                    WhatsApp
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12.55a11 11 0 0 1 14.08 0" />
                            <path d="M1.42 9a16 16 0 0 1 21.16 0" />
                            <path d="M8.53 16.11a6 6 0 0 1 6.95 0" />
                            <line x1="12" y1="20" x2="12.01" y2="20" />
                        </svg>
                    </span>
                    Wi-Fi
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="23 7 16 12 23 17 23 7" />
                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
                        </svg>
                    </span>
                    Zoom
                </span>
            </div>

            {{-- Form fields --}}
            <h3 class="content-label">Conteúdo</h3>
            <form id="pix-form" data-pix-form class="row g-3">

                <div class="col-md-4">
                    <label for="pix-key-type" class="form-label">Tipo de chave</label>
                    <select id="pix-key-type" class="form-select" data-pix-key-type>
                        <option value="phone">Telefone</option>
                        <option value="cpf">CPF</option>
                        <option value="cnpj">CNPJ</option>
                        <option value="email">Email</option>
                        <option value="random">Aleatória</option>
                    </select>
                </div>

                <div class="col-md-8">
                    <label for="pix-key" class="form-label">Chave Pix</label>
                    <input id="pix-key" class="form-control" type="text" required data-pix-key>
                </div>

                <div class="col-md-6">
                    <label for="pix-name" class="form-label">Nome do beneficiário <small
                            class="text-body-secondary fw-normal">(até 25)</small></label>
                    <input id="pix-name" class="form-control" type="text" maxlength="40" required data-pix-name>
                </div>

                <div class="col-md-6">
                    <label for="pix-city" class="form-label">Cidade <small class="text-body-secondary fw-normal">(até
                            15)</small></label>
                    <input id="pix-city" class="form-control" type="text" maxlength="30" required data-pix-city>
                </div>

                <div class="col-md-6">
                    <label for="pix-amount" class="form-label">Valor <small
                            class="text-body-secondary fw-normal">(opcional)</small></label>
                    <input id="pix-amount" class="form-control" type="text" inputmode="decimal" placeholder="19,90"
                        data-pix-amount>
                </div>

                <div class="col-md-6">
                    <label for="pix-txid" class="form-label">TXID <small class="text-body-secondary fw-normal">(padrão
                            ***)</small></label>
                    <input id="pix-txid" class="form-control" type="text" maxlength="25" placeholder="PEDIDO123"
                        data-pix-txid>
                </div>

                <div class="col-12">
                    <p class="feedback" data-pix-feedback aria-live="polite"></p>
                </div>

            </form>
        </div>
    </div>

    {{-- Right column: QR preview --}}
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
                    </svg>
                </div>
                <div class="qr-sample-box">
                    <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg" width="72" height="72">
                        <rect x="4" y="4" width="30" height="30" rx="3" fill="#c4cfdf" />
                        <rect x="10" y="10" width="18" height="18" fill="#e2e8f0" rx="1.5" />
                        <rect x="46" y="4" width="30" height="30" rx="3" fill="#c4cfdf" />
                        <rect x="52" y="10" width="18" height="18" fill="#e2e8f0" rx="1.5" />
                        <rect x="4" y="46" width="30" height="30" rx="3" fill="#c4cfdf" />
                        <rect x="10" y="52" width="18" height="18" fill="#e2e8f0" rx="1.5" />
                        <rect x="46" y="46" width="8" height="8" fill="#c4cfdf" />
                        <rect x="58" y="58" width="8" height="8" fill="#c4cfdf" />
                    </svg>
                </div>
                <div class="qr-sample-box">
                    <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg" width="72" height="72">
                        <rect x="4" y="4" width="30" height="30" rx="3" fill="#adbcd0" />
                        <rect x="10" y="10" width="18" height="18" fill="#d8e2ed" rx="1.5" />
                        <rect x="46" y="4" width="30" height="30" rx="3" fill="#adbcd0" />
                        <rect x="52" y="10" width="18" height="18" fill="#d8e2ed" rx="1.5" />
                        <rect x="4" y="46" width="30" height="30" rx="3" fill="#adbcd0" />
                        <rect x="10" y="52" width="18" height="18" fill="#d8e2ed" rx="1.5" />
                        <rect x="58" y="48" width="8" height="8" fill="#adbcd0" />
                    </svg>
                </div>
                <div class="qr-sample-box">
                    <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg" width="72" height="72">
                        <rect x="4" y="4" width="30" height="30" rx="3" fill="#bdc8d8" />
                        <rect x="10" y="10" width="18" height="18" fill="#dde6f0" rx="1.5" />
                        <rect x="46" y="4" width="30" height="30" rx="3" fill="#bdc8d8" />
                        <rect x="52" y="10" width="18" height="18" fill="#dde6f0" rx="1.5" />
                        <rect x="4" y="46" width="30" height="30" rx="3" fill="#bdc8d8" />
                        <rect x="10" y="52" width="18" height="18" fill="#dde6f0" rx="1.5" />
                        <rect x="46" y="46" width="8" height="8" fill="#bdc8d8" />
                    </svg>
                </div>
            </div>

            <div class="qr-result-area" data-pix-result hidden>
                <img src="" alt="QR Code Pix" data-pix-qr>

                <div class="w-100">
                    <label for="pix-payload" class="form-label">Pix copia e cola</label>
                    <textarea id="pix-payload" class="form-control payload-output" readonly data-pix-payload></textarea>
                </div>

                <div class="d-flex align-items-center gap-3 w-100">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                        data-pix-copy>Copiar código</button>
                    <span class="feedback small" data-pix-copy-feedback aria-live="polite"></span>
                </div>
            </div>

            <button type="submit" form="pix-form" class="btn-generate">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                Gerar QR Code Pix
            </button>
        </div>
    </div>

</div>
@endsection