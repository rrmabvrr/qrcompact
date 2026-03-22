@extends('layouts.app', ['title' => 'Links Curtos', 'page' => 'links'])

@section('content')
<div class="page-grid">

    {{-- Left column --}}
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
                {{-- Row 1 --}}
                <a class="type-card active" href="{{ route('links.index') }}">
                    <span class="type-card-icon">
                        <i class="bi bi-link-45deg" aria-hidden="true"></i>
                    </span>
                    Link Único
                </a>
                <a class="type-card" href="{{ route('pix.index') }}">
                    <span class="type-card-icon">
                        <i class="bi bi-currency-exchange" aria-hidden="true"></i>
                    </span>
                    Pix
                </a>
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-card-text" aria-hidden="true"></i>
                    </span>
                    Texto
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-telephone" aria-hidden="true"></i>
                    </span>
                    Chamada
                </span>
                {{-- Row 2 --}}
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-envelope" aria-hidden="true"></i>
                    </span>
                    Email
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-chat-left-text" aria-hidden="true"></i>
                    </span>
                    SMS
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-currency-bitcoin" aria-hidden="true"></i>
                    </span>
                    Bitcoin
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-paypal" aria-hidden="true"></i>
                    </span>
                    PayPal
                </span>
                {{-- Row 3 --}}
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-person-vcard" aria-hidden="true"></i>
                    </span>
                    V-Card
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-whatsapp" aria-hidden="true"></i>
                    </span>
                    WhatsApp
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-wifi" aria-hidden="true"></i>
                    </span>
                    Wi-Fi
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-camera-video" aria-hidden="true"></i>
                    </span>
                    Zoom
                </span>
                {{-- Row 4 --}}
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-skype" aria-hidden="true"></i>
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
                <i class="bi bi-eye" aria-hidden="true"></i>
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