@extends('layouts.app', ['title' => 'Gerar QR Code Pix', 'page' => 'pix'])

@section('content')
    <section class="row g-4 align-items-stretch mt-1">
        <div class="col-lg-8">
            <article class="glass-card p-4 p-lg-5 h-100">
                <p class="section-kicker mb-3">Pix copia e cola + QR Code</p>
                <h1 class="headline">Monte o payload BR Code no backend e entregue o QR pronto para pagamento.</h1>
                <p class="hero-copy mt-3 mb-0">Chave formatada por tipo, nome e cidade saneados, valor opcional com duas casas, TXID controlado e CRC16-CCITT calculado no servidor.</p>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <span class="chip-pill">GUI BR.GOV.BCB.PIX</span>
                    <span class="chip-pill">Valor aberto ou fixo</span>
                    <span class="chip-pill">CRC16-CCITT</span>
                </div>
            </article>
        </div>

        <div class="col-lg-4">
            <aside class="d-grid gap-3 h-100">
                <div class="glass-card metric-card p-4">
                    <strong>25</strong>
                    <span>caracteres maximos para nome e TXID</span>
                </div>
                <div class="glass-card metric-card p-4">
                    <strong>15</strong>
                    <span>caracteres maximos para cidade sanitizada</span>
                </div>
            </aside>
        </div>
    </section>

    <section class="row g-4 mt-1">
        <div class="col-lg-7">
            <article class="card glass-card border-0 h-100">
                <div class="card-body p-4 p-lg-5">
                    <div class="mb-4">
                        <h2 class="h3 mb-2">Dados Pix</h2>
                        <p class="text-body-secondary mb-0">Todos os campos sao processados no backend antes da geracao do QR.</p>
                    </div>

                    <form class="row g-3" data-pix-form>
                        <div class="col-md-4">
                            <label for="pix-key-type" class="form-label fw-semibold">Tipo de chave</label>
                            <select id="pix-key-type" class="form-select" data-pix-key-type>
                                <option value="phone">Telefone</option>
                                <option value="cpf">CPF</option>
                                <option value="cnpj">CNPJ</option>
                                <option value="email">Email</option>
                                <option value="random">Aleatoria</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label for="pix-key" class="form-label fw-semibold">Chave Pix</label>
                            <input id="pix-key" class="form-control" type="text" required data-pix-key>
                        </div>

                        <div class="col-md-6">
                            <label for="pix-name" class="form-label fw-semibold">Nome do beneficiario <small class="text-body-secondary">(obrigatorio, ate 25)</small></label>
                            <input id="pix-name" class="form-control" type="text" maxlength="40" required data-pix-name>
                        </div>

                        <div class="col-md-6">
                            <label for="pix-city" class="form-label fw-semibold">Cidade do beneficiario <small class="text-body-secondary">(obrigatoria, ate 15)</small></label>
                            <input id="pix-city" class="form-control" type="text" maxlength="30" required data-pix-city>
                        </div>

                        <div class="col-md-6">
                            <label for="pix-amount" class="form-label fw-semibold">Valor <small class="text-body-secondary">(opcional)</small></label>
                            <input id="pix-amount" class="form-control" type="text" inputmode="decimal" placeholder="19,90" data-pix-amount>
                        </div>

                        <div class="col-md-6">
                            <label for="pix-txid" class="form-label fw-semibold">TXID <small class="text-body-secondary">(padrao ***)</small></label>
                            <input id="pix-txid" class="form-control" type="text" maxlength="25" placeholder="PEDIDO123" data-pix-txid>
                        </div>

                        <div class="col-12 d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                            <button class="btn btn-primary btn-lg rounded-pill px-4" type="submit">Gerar Pix</button>
                            <p class="feedback small mb-0 text-body-secondary" data-pix-feedback aria-live="polite"></p>
                        </div>
                    </form>
                </div>
            </article>
        </div>

        <div class="col-lg-5">
            <article class="card glass-card border-0 h-100">
                <div class="card-body p-4 p-lg-5 d-flex flex-column">
                    <div class="mb-4">
                        <h2 class="h3 mb-2">Resultado</h2>
                        <p class="text-body-secondary mb-0">Copie o payload ou compartilhe o QR Code imediatamente.</p>
                    </div>

                    <section class="result-stack" data-pix-result hidden>
                        <img src="" alt="QR Code Pix" class="qr-image mx-auto d-block" data-pix-qr>

                        <div>
                            <label for="pix-payload" class="form-label fw-semibold">Pix copia e cola</label>
                            <textarea id="pix-payload" class="form-control payload-output" readonly data-pix-payload></textarea>
                        </div>

                        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-pix-copy>Copiar codigo Pix</button>
                            <p class="small text-body-secondary mb-0" data-pix-copy-feedback aria-live="polite"></p>
                        </div>
                    </section>

                    <p class="text-body-secondary small mt-4 mb-0">Se o valor ficar em branco, o QR Code sera gerado com valor aberto.</p>
                </div>
            </article>
        </div>
    </section>
@endsection