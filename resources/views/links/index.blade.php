@extends('layouts.app', ['title' => 'Links Curtos', 'page' => 'links'])

@section('content')
    <section class="row g-4 mt-1 align-items-stretch links-dashboard">
        <div class="col-xl-4">
            <article class="glass-card p-4 p-lg-5 h-100 links-hero-card">
                <p class="section-kicker mb-3">Encurtador + QR no mesmo fluxo</p>
                <h1 class="headline">Crie links curtos que ja saem prontos para compartilhar e escanear.</h1>
                <p class="hero-copy mt-3 mb-0">O QRCompact gera slug aleatorio, QR Code no backend e um painel direto para revisar, editar e reutilizar seus ultimos 100 links.</p>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <span class="chip-pill">Slug alfanumerico de 6 caracteres</span>
                    <span class="chip-pill">Redirecionamento 302</span>
                    <span class="chip-pill">QR no backend em PHP</span>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-sm-6 col-xl-12">
                        <div class="glass-card metric-card p-4 h-100">
                            <strong>100</strong>
                            <span>ultimos links listados automaticamente</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-12">
                        <div class="glass-card metric-card p-4 h-100">
                            <strong>10</strong>
                            <span>tentativas maximas para gerar slug unico</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-xl-8">
            <div class="row g-4 h-100">
                <div class="col-12">
                    <article class="card glass-card border-0 h-100">
                        <div class="card-body p-4 p-lg-5">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
                                <div>
                                    <h2 class="h3 mb-2">Novo link curto</h2>
                                    <p class="text-body-secondary mb-0">Informe uma URL completa iniciando com http:// ou https:// e gere um atalho compartilhavel em um fluxo unico.</p>
                                </div>
                                <div class="small text-body-secondary fw-semibold text-uppercase">Criacao instantanea + QR</div>
                            </div>

                            <form class="row g-3 align-items-end" data-links-form>
                                <div class="col-lg-8">
                                    <label for="short-url" class="form-label fw-semibold">URL de destino</label>
                                    <input id="short-url" class="form-control form-control-lg" type="url" name="url" placeholder="https://seusite.com/promocao" required data-links-url>
                                </div>

                                <div class="col-sm-6 col-lg-4 d-grid">
                                    <button class="btn btn-primary btn-lg rounded-pill px-4" type="submit">Gerar link curto</button>
                                </div>

                                <div class="col-12">
                                    <p class="feedback small mb-0 text-body-secondary" data-links-feedback aria-live="polite"></p>
                                </div>
                            </form>

                            <section class="result-panel result-panel--horizontal mt-4" data-links-result hidden>
                                <div class="result-panel__content">
                                    <p class="text-body-secondary text-uppercase small fw-semibold mb-2">Link curto gerado</p>
                                    <a href="#" target="_blank" rel="noreferrer" class="link-short d-inline-block text-decoration-none fs-5 fw-semibold" data-result-link></a>
                                    <p class="small text-body-secondary mb-0 mt-3">Abra, compartilhe ou escaneie o QR ao lado sem sair da tela.</p>
                                </div>
                                <div class="result-panel__media">
                                    <img src="" alt="QR Code do link curto" class="qr-image" data-result-qr>
                                </div>
                            </section>
                        </div>
                    </article>
                </div>

                <div class="col-12">
                    <article class="card glass-card border-0 h-100">
                        <div class="card-body p-4 p-lg-5">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
                                <div>
                                    <h2 class="h3 mb-2">Ultimos links</h2>
                                    <p class="text-body-secondary mb-0">Do mais recente para o mais antigo, com consulta de QR e edicao por slug em uma lista lateralizada.</p>
                                </div>
                                <div class="small text-body-secondary fw-semibold text-uppercase">Historico operacional</div>
                            </div>

                            <div class="link-list d-grid gap-3" data-links-list></div>
                            <div class="alert alert-light border empty-state mt-3 mb-0" data-links-empty>
                                Nenhum link curto criado ainda.
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" data-detail-modal tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content glass-card border-0">
                <div class="modal-header border-0 pb-0 px-4 px-lg-5 pt-4">
                    <div>
                        <h3 class="h4 mb-2">Detalhes do link <span data-detail-title></span></h3>
                        <p class="text-body-secondary mb-0">QR Code atualizado a partir do slug salvo.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body px-4 px-lg-5 pb-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="detail-card h-100 p-3">
                                <span class="detail-label">Link curto</span>
                                <a href="#" target="_blank" rel="noreferrer" class="text-decoration-none" data-detail-url></a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card h-100 p-3">
                                <span class="detail-label">Destino</span>
                                <div data-detail-destination></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card h-100 p-3">
                                <span class="detail-label">Criado em</span>
                                <div data-detail-created></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card h-100 p-3">
                                <span class="detail-label">Atualizado em</span>
                                <div data-detail-updated></div>
                            </div>
                        </div>
                    </div>

                    <img src="" alt="QR Code do link curto" class="qr-image mx-auto d-block" data-detail-qr>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-edit-modal tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border-0">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h3 class="h4 mb-2">Editar destino do slug <span data-edit-slug></span></h3>
                        <p class="text-body-secondary mb-0">O slug nao muda. Apenas o destino do redirecionamento.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancelar"></button>
                </div>

                <form class="modal-body px-4 pb-4" data-edit-form>
                    <div class="mb-3">
                        <label for="edit-url" class="form-label fw-semibold">Nova URL de destino</label>
                        <input id="edit-url" class="form-control" type="url" required data-edit-url>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <p class="feedback small mb-0 text-body-secondary" data-edit-feedback aria-live="polite"></p>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Salvar alteracoes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection