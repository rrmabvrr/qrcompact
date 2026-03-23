@extends('layouts.app', ['title' => 'Gerar QR Code Pix', 'page' => 'pix'])

@section('form-content')
<form id="pix-form" data-pix-form class="row g-3">

    <div class="col-md-4">
        <label for="pix-key-type" class="form-label">Tipo de chave</label>
        <div class="position-relative">
            <select id="pix-key-type" class="form-select pe-5" style="appearance:none;" data-pix-key-type>
                <option value="phone">Telefone</option>
                <option value="cpf">CPF</option>
                <option value="cnpj">CNPJ</option>
                <option value="email">Email</option>
                <option value="random">Aleatória</option>
            </select>
            <i class="bi bi-chevron-down position-absolute top-50 end-0 translate-middle-y pe-none me-3" aria-hidden="true"></i>
        </div>
    </div>

    <div class="col-md-8">
        <label for="pix-key" class="form-label">Chave Pix</label>
        <input id="pix-key" class="form-control" type="text" required data-pix-key>
    </div>

    <div class="col-md-6">
        <label for="pix-name" class="form-label">Nome do beneficiário <small class="text-body-secondary fw-normal">(até
                25)</small></label>
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
        <input id="pix-amount" class="form-control" type="text" inputmode="decimal" placeholder="19,90" data-pix-amount>
    </div>

    <div class="col-md-6">
        <label for="pix-txid" class="form-label">TXID <small class="text-body-secondary fw-normal">(padrão
                ***)</small></label>
        <input id="pix-txid" class="form-control" type="text" maxlength="25" placeholder="PEDIDO123" data-pix-txid>
    </div>

    <div class="col-12">
        <p class="feedback" data-pix-feedback aria-live="polite"></p>
    </div>
</form>
@endsection

@section('qr-result-content')
<div class="qr-result-area" data-pix-result hidden>
    <img src="" alt="QR Code Pix" data-pix-qr>

    <dl class="pix-summary w-100">
        <div class="pix-summary-row">
            <dt>Chave Pix</dt>
            <dd data-pix-summary-key>—</dd>
        </div>
        <div class="pix-summary-row">
            <dt>Beneficiário</dt>
            <dd data-pix-summary-name>—</dd>
        </div>
        <div class="pix-summary-row">
            <dt>Cod. da Transação</dt>
            <dd data-pix-summary-txid>—</dd>
        </div>
    </dl>

    <div class="w-100">
        <label for="pix-payload" class="form-label">Pix copia e cola</label>
        <textarea id="pix-payload" class="form-control payload-output" readonly data-pix-payload></textarea>
    </div>

    <div class="d-flex align-items-center gap-3 w-100">
        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-pix-copy>Copiar
            código</button>
        <span class="feedback small" data-pix-copy-feedback aria-live="polite"></span>
    </div>
</div>
@endsection

@section('generate-form-id', 'pix-form')
@section('generate-label', 'Gerar QR Code Pix')

@section('content')
@endsection