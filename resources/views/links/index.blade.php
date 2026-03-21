@extends('layouts.app', ['title' => 'QRCompact | Links Curtos', 'page' => 'links'])

@section('content')
    <section class="hero">
        <article class="hero__main">
            <p class="eyebrow">Encurtador + QR no mesmo fluxo</p>
            <h1 class="headline">Crie links curtos que ja saem prontos para compartilhar e escanear.</h1>
            <p class="hero__copy">O QRCompact gera slug aleatorio, QR Code no backend e um painel direto para revisar, editar e reutilizar seus ultimos 100 links.</p>

            <div class="hero__chips">
                <span class="chip">Slug alfanumerico de 6 caracteres</span>
                <span class="chip">Redirecionamento 302</span>
                <span class="chip">QR no backend em PHP</span>
            </div>
        </article>

        <aside class="hero__aside">
            <div class="metric">
                <strong>100</strong>
                <span>ultimos links listados automaticamente</span>
            </div>
            <div class="metric">
                <strong>10</strong>
                <span>tentativas maximas para gerar slug unico</span>
            </div>
        </aside>
    </section>

    <section class="grid">
        <article class="panel">
            <div class="panel__header">
                <div>
                    <h2>Novo link curto</h2>
                    <p>Informe uma URL completa iniciando com http:// ou https://.</p>
                </div>
            </div>

            <form class="stack" data-links-form>
                <div class="field">
                    <label for="short-url">URL de destino</label>
                    <input id="short-url" class="control" type="url" name="url" placeholder="https://seusite.com/promocao" required data-links-url>
                </div>

                <div class="actions-row">
                    <button class="button" type="submit">Gerar link curto</button>
                    <p class="feedback" data-links-feedback aria-live="polite"></p>
                </div>
            </form>

            <section class="result-card" data-links-result hidden>
                <div>
                    <p class="result-card__caption">Link curto gerado</p>
                    <a href="#" target="_blank" rel="noreferrer" class="result-card__link" data-result-link></a>
                </div>
                <img src="" alt="QR Code do link curto" class="result-card__media" data-result-qr>
            </section>
        </article>

        <article class="panel">
            <div class="panel__header">
                <div>
                    <h2>Ultimos links</h2>
                    <p>Do mais recente para o mais antigo, com consulta de QR e edicao por slug.</p>
                </div>
            </div>

            <div class="link-list" data-links-list></div>
            <div class="empty-state" data-links-empty>
                Nenhum link curto criado ainda.
            </div>
        </article>
    </section>

    <div class="modal" data-detail-modal hidden>
        <div class="modal__dialog">
            <div class="modal__header">
                <div>
                    <h3>Detalhes do link <span data-detail-title></span></h3>
                    <p>QR Code atualizado a partir do slug salvo.</p>
                </div>
                <button type="button" class="button button--ghost" data-close-modal>Fechar</button>
            </div>

            <div class="modal__content">
                <div class="detail-grid">
                    <div class="detail-grid__item">
                        <span class="detail-grid__label">Link curto</span>
                        <a href="#" target="_blank" rel="noreferrer" data-detail-url></a>
                    </div>
                    <div class="detail-grid__item">
                        <span class="detail-grid__label">Destino</span>
                        <div data-detail-destination></div>
                    </div>
                    <div class="detail-grid__item">
                        <span class="detail-grid__label">Criado em</span>
                        <div data-detail-created></div>
                    </div>
                    <div class="detail-grid__item">
                        <span class="detail-grid__label">Atualizado em</span>
                        <div data-detail-updated></div>
                    </div>
                </div>

                <img src="" alt="QR Code do link curto" class="result-card__media" data-detail-qr>
            </div>
        </div>
    </div>

    <div class="modal" data-edit-modal hidden>
        <div class="modal__dialog">
            <div class="modal__header">
                <div>
                    <h3>Editar destino do slug <span data-edit-slug></span></h3>
                    <p>O slug nao muda. Apenas o destino do redirecionamento.</p>
                </div>
                <button type="button" class="button button--ghost" data-close-modal>Cancelar</button>
            </div>

            <form class="modal__content" data-edit-form>
                <div class="field">
                    <label for="edit-url">Nova URL de destino</label>
                    <input id="edit-url" class="control" type="url" required data-edit-url>
                </div>

                <div class="modal__footer">
                    <p class="feedback" data-edit-feedback aria-live="polite"></p>
                    <button type="submit" class="button">Salvar alteracoes</button>
                </div>
            </form>
        </div>
    </div>
@endsection