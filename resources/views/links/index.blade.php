@extends('layouts.app', ['title' => 'Links Curtos', 'page' => 'links'])

@section('form-content')
<form id="links-form" data-links-form>
    <div class="mb-2">

        <label for="link-name" class="form-label">Nome</label>
        <input id="link-name" class="form-control" type="text" name="name"
            placeholder="Nome do link (opcional - será gerado automaticamente se vazio)" data-links-name>
        <label for="link-url" class="form-label">URL</label>
        <input id="link-url" class="form-control" type="url" name="url" placeholder="http://..." required
            data-links-url>
    </div>

    <div class="mb-2" data-whatsapp-fields hidden>
        <label for="wa-phone" class="form-label">Digite seu numero de telefone WhatsApp</label>
        <input id="wa-phone" class="form-control" type="text" inputmode="numeric" placeholder="5599999999999" value="55"
            data-wa-phone>

        <label for="wa-message" class="form-label mt-2">Mensagem personalizada</label>
        <textarea id="wa-message" class="form-control" rows="3" placeholder="Ola! Vim pelo QR Code."
            data-wa-message></textarea>
        <div class="form-text">Formato do numero: codigo do pais + DDD + numero. Exemplo: 5599999999999</div>
    </div>

    <p class="feedback mt-2" data-links-feedback aria-live="polite"></p>
</form>
@endsection

@section('left-column-extra')
<div class="qrc-card">
    <h2 class="section-heading">Últimos links</h2>
    <div data-links-list></div>
    <div class="empty-state" data-links-empty>Nenhum link criado ainda.</div>
</div>
@endsection

@section('qr-result-content')
<div class="qr-result-area" data-links-result hidden>
    <p class="mb-2">Nome: <span data-result-name></span></p>
    <img src="" alt="QR Code gerado" data-result-qr>
    <a href="#" target="_blank" rel="noreferrer" class="qr-result-link" data-result-link></a>
</div>
@endsection

@section('generate-form-id', 'links-form')
@section('generate-label', 'Gerar QRCode')

@section('content')

{{-- Detail modal --}}
<div class="modal fade" data-detail-modal tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h3 class="h5 mb-1">Detalhes do link <span data-detail-title></span></h3>
                    <p class="text-body-secondary small mb-0">QR Code gerado a partir do nome salvo.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="detail-card">
                            <p class="detail-label">Nome</p>
                            <div class="small text-break" data-detail-name></div>
                            <hr>
                            <span class="detail-label">Link curto</span>
                            <a href="#" target="_blank" rel="noreferrer" class="text-decoration-none small"
                                data-detail-url></a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <span class="detail-label">Criado em:</span>
                            <div class="small" data-detail-created></div>
                            <hr>
                            <span class="detail-label">Atualizado em:</span>
                            <div class="small" data-detail-updated></div>
                        </div>
                    </div>
                </div>

                <img src="" alt="QR Code do link" class="qr-image" data-detail-qr>

                <div class="col-md-12 mt-4">
                    <div class="detail-card">
                        <span class="detail-label">Destino</span>
                        <div class="small text-break" data-detail-destination></div>
                    </div>
                </div>

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
                    <h3 class="h5 mb-1">Editar link: <span data-edit-nome></span></h3>
                    <p class="text-body-secondary small mb-0">Nome e destino do redirecionamento podem ser alterados.
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancelar"></button>
            </div>
            <form class="modal-body px-4 pb-4" data-edit-form>
                <div class="mb-3">
                    <label for="edit-name" class="form-label">Editar Nome do Link</label>
                    <input id="edit-name" class="form-control" type="text" data-edit-name>
                    <hr>
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