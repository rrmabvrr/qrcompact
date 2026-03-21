@extends('layouts.app', ['title' => 'Links Curtos', 'page' => 'links'])

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
                {{-- Row 1 --}}
                <a class="type-card active" href="{{ route('links.index') }}">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                        </svg>
                    </span>
                    Link Único
                </a>
                <a class="type-card" href="{{ route('pix.index') }}">
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
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                    </span>
                    Texto
                </span>
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
                {{-- Row 2 --}}
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
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            <line x1="9" y1="10" x2="15" y2="10" />
                        </svg>
                    </span>
                    SMS
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </span>
                    Bitcoin
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            <path d="M8 10h8" />
                            <path d="M8 14h5" />
                        </svg>
                    </span>
                    PayPal
                </span>
                {{-- Row 3 --}}
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
                {{-- Row 4 --}}
                <span class="type-card">
                    <span class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 2H3v16h5v4l4-4h5l4-4V2z" />
                            <path d="M11.5 7.5a1 1 0 1 0 2 0 1 1 0 0 0-2 0" />
                            <path d="M11.5 12a1 1 0 1 0 2 0 1 1 0 0 0-2 0" />
                        </svg>
                    </span>
                    Skype
                </span>
            </div>

            {{-- Conteúdo --}}
            <h3 class="content-label">Conteúdo</h3>
            <form id="links-form" data-links-form>
                <div class="mb-2">
                    <label for="short-url" class="form-label">URL</label>
                    <input id="short-url" class="form-control" type="url" name="url" placeholder="http://..." required
                        data-links-url>
                </div>
                <p class="feedback mt-2" data-links-feedback aria-live="polite"></p>
            </form>
        </div>

        {{-- Links list --}}
        <div class="qrc-card">
            <h2 class="section-heading">Últimos links</h2>
            <div data-links-list></div>
            <div class="empty-state" data-links-empty>Nenhum link criado ainda.</div>
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
                        <rect x="70" y="58" width="6" height="6" fill="#b8c5d6" />
                        <rect x="58" y="70" width="8" height="6" fill="#b8c5d6" />
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
                        <rect x="70" y="46" width="8" height="8" fill="#c4cfdf" />
                        <rect x="46" y="70" width="8" height="6" fill="#c4cfdf" />
                        <rect x="66" y="70" width="10" height="6" fill="#c4cfdf" />
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
                        <rect x="48" y="58" width="8" height="8" fill="#adbcd0" />
                        <rect x="58" y="70" width="8" height="6" fill="#adbcd0" />
                        <rect x="70" y="58" width="6" height="18" fill="#adbcd0" />
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
                        <rect x="58" y="46" width="18" height="8" fill="#bdc8d8" />
                        <rect x="46" y="58" width="18" height="8" fill="#bdc8d8" />
                        <rect x="68" y="66" width="8" height="10" fill="#bdc8d8" />
                        <rect x="46" y="70" width="18" height="6" fill="#bdc8d8" />
                    </svg>
                </div>
            </div>

            <div class="qr-result-area" data-links-result hidden>
                <img src="" alt="QR Code gerado" data-result-qr>
                <a href="#" target="_blank" rel="noreferrer" class="qr-result-link" data-result-link></a>
            </div>

            <button type="submit" form="links-form" class="btn-generate">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                Gerar QRCode
            </button>
        </div>
    </div>

</div>

{{-- Detail modal --}}
<div class="modal fade" data-detail-modal tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h3 class="h5 mb-1">Detalhes do link <span data-detail-title></span></h3>
                    <p class="text-body-secondary small mb-0">QR Code gerado a partir do slug salvo.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="detail-card">
                            <span class="detail-label">Link curto</span>
                            <a href="#" target="_blank" rel="noreferrer" class="text-decoration-none small"
                                data-detail-url></a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <span class="detail-label">Destino</span>
                            <div class="small text-break" data-detail-destination></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <span class="detail-label">Criado em</span>
                            <div class="small" data-detail-created></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <span class="detail-label">Atualizado em</span>
                            <div class="small" data-detail-updated></div>
                        </div>
                    </div>
                </div>
                <img src="" alt="QR Code do link" class="qr-image" data-detail-qr>
            </div>
        </div>
    </div>
</div>

{{-- Edit modal --}}
<div class="modal fade" data-edit-modal tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h3 class="h5 mb-1">Editar slug <span data-edit-slug></span></h3>
                    <p class="text-body-secondary small mb-0">Apenas o destino do redirecionamento será alterado.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancelar"></button>
            </div>
            <form class="modal-body px-4 pb-4" data-edit-form>
                <div class="mb-3">
                    <label for="edit-url" class="form-label">Nova URL de destino</label>
                    <input id="edit-url" class="form-control" type="url" required data-edit-url>
                </div>
                <div class="d-flex justify-content-between align-items-center gap-3">
                    <p class="feedback small mb-0" data-edit-feedback aria-live="polite"></p>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
