@extends('layouts.app', ['title' => 'Gerar QR Code Pix', 'page' => 'pix'])

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
                <a class="type-card" href="{{ route('links.index') }}">
                    <span class="type-card-icon">
                        <i class="bi bi-link-45deg" aria-hidden="true"></i>
                    </span>
                    Link Único
                </a>
                <a class="type-card active" href="{{ route('pix.index') }}">
                    <span class="type-card-icon">
                        <i class="bi bi-currency-exchange" aria-hidden="true"></i>
                    </span>
                    Pix
                </a>
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-telephone" aria-hidden="true"></i>
                    </span>
                    Chamada
                </span>
                <span class="type-card">
                    <span class="type-card-icon">
                        <i class="bi bi-envelope" aria-hidden="true"></i>
                    </span>
                    Email
                </span>
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
                <i class="bi bi-eye" aria-hidden="true"></i>
                Gerar QR Code Pix
            </button>
        </div>
    </div>

</div>
@endsection