@extends('layouts.app', ['title' => 'Gerar QR Code Pix', 'page' => 'pix'])

@section('content')
    <section class="hero">
        <article class="hero__main">
            <p class="eyebrow">Pix copia e cola + QR Code</p>
            <h1 class="headline">Monte o payload BR Code no backend e entregue o QR pronto para pagamento.</h1>
            <p class="hero__copy">Chave formatada por tipo, nome e cidade saneados, valor opcional com duas casas, TXID controlado e CRC16-CCITT calculado no servidor.</p>

            <div class="hero__chips">
                <span class="chip">GUI BR.GOV.BCB.PIX</span>
                <span class="chip">Valor aberto ou fixo</span>
                <span class="chip">CRC16-CCITT</span>
            </div>
        </article>

        <aside class="hero__aside">
            <div class="metric">
                <strong>25</strong>
                <span>caracteres maximos para nome e TXID</span>
            </div>
            <div class="metric">
                <strong>15</strong>
                <span>caracteres maximos para cidade sanitizada</span>
            </div>
        </aside>
    </section>

    <section class="pix-layout">
        <article class="panel">
            <div class="panel__header">
                <div>
                    <h2>Dados Pix</h2>
                    <p>Todos os campos sao processados no backend antes da geracao do QR.</p>
                </div>
            </div>

            <form class="stack" data-pix-form>
                <div class="field field--inline">
                    <label for="pix-key-type">Tipo de chave</label>
                    <select id="pix-key-type" class="select" data-pix-key-type>
                        <option value="phone">Telefone</option>
                        <option value="cpf">CPF</option>
                        <option value="cnpj">CNPJ</option>
                        <option value="email">Email</option>
                        <option value="random">Aleatoria</option>
                    </select>
                </div>

                <div class="field">
                    <label for="pix-key">Chave Pix</label>
                    <input id="pix-key" class="control" type="text" required data-pix-key>
                </div>

                <div class="field">
                    <label for="pix-name">Nome do beneficiario <small>(obrigatorio, ate 25)</small></label>
                    <input id="pix-name" class="control" type="text" maxlength="40" required data-pix-name>
                </div>

                <div class="field">
                    <label for="pix-city">Cidade do beneficiario <small>(obrigatoria, ate 15)</small></label>
                    <input id="pix-city" class="control" type="text" maxlength="30" required data-pix-city>
                </div>

                <div class="field field--inline">
                    <label for="pix-amount">Valor <small>(opcional)</small></label>
                    <input id="pix-amount" class="control" type="text" inputmode="decimal" placeholder="19,90" data-pix-amount>
                </div>

                <div class="field field--inline">
                    <label for="pix-txid">TXID <small>(padrao ***)</small></label>
                    <input id="pix-txid" class="control" type="text" maxlength="25" placeholder="PEDIDO123" data-pix-txid>
                </div>

                <div class="actions-row">
                    <button class="button" type="submit">Gerar Pix</button>
                    <p class="feedback" data-pix-feedback aria-live="polite"></p>
                </div>
            </form>
        </article>

        <article class="panel">
            <div class="panel__header">
                <div>
                    <h2>Resultado</h2>
                    <p>Copie o payload ou compartilhe o QR Code imediatamente.</p>
                </div>
            </div>

            <section class="stack" data-pix-result hidden>
                <img src="" alt="QR Code Pix" class="result-card__media" data-pix-qr>

                <div class="field">
                    <label for="pix-payload">Pix copia e cola</label>
                    <textarea id="pix-payload" class="textarea" readonly data-pix-payload></textarea>
                </div>

                <div class="actions-row">
                    <button type="button" class="button button--ghost" data-pix-copy>Copiar codigo Pix</button>
                    <p class="copy-feedback" data-pix-copy-feedback aria-live="polite"></p>
                </div>
            </section>

            <p class="helper">Se o valor ficar em branco, o QR Code sera gerado com valor aberto.</p>
        </article>
    </section>
@endsection