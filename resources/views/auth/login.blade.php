@extends('layouts.guest', ['title' => 'Entrar - QRCompact'])

@section('content')
    <h1 class="guest-title">Acesse sua conta</h1>
    <p class="guest-subtitle">Escolha o método de acesso para entrar na sua conta.</p>

    @if (session('status'))
        <div class="alert alert-info" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif

    {{-- Painel: login por senha --}}
    <div id="panel-password" class="login-panel" @if(old('_panel') !== 'password') style="display:none" @endif>
        <form action="{{ route('login.password') }}" method="POST" class="row g-3">
            @csrf
            <input type="hidden" name="_panel" value="password">

            <div class="col-12">
                <label for="email-pass" class="form-label">Email</label>
                <input
                    id="email-pass"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    class="form-control"
                    placeholder="email@exemplo.com"
                    required
                    autofocus>
            </div>

            <div class="col-12">
                <label for="password" class="form-label">Senha</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="form-control"
                    placeholder=""
                    required>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-generate w-100 justify-content-center">
                    Entrar com senha
                </button>
            </div>
        </form>

        <div class="text-center mt-3">
            <button type="button" class="btn btn-secondary btn-link-subtle" onclick="showLoginPanel('code')">
                <i class="bi bi-envelope me-1"></i>Usar código por e-mail
            </button>
        </div>
    </div>

    {{-- Painel: login por código (padrão) --}}
    <div id="panel-code" class="login-panel" @if(old('_panel') === 'password') style="display:none" @endif>
        <button type="button" class="btn btn-outline-secondary btn-outline-login w-100 mt-1" onclick="showLoginPanel('password')">
            <u class="me-2">***</u> Acessar com senha
        </button>

        <div class="login-or my-3">
            <span>Ou acesse recebendo um<br>código de acesso por e-mail</span>
        </div>

        <form action="{{ route('login.send-code') }}" method="POST" class="row g-3">
            @csrf
            <input type="hidden" name="_panel" value="code">

            <div class="col-12">
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    class="form-control"
                    placeholder="email@exemplo.com"
                    required
                    autofocus>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-generate w-100 justify-content-center">
                    Enviar código
                </button>
            </div>
        </form>
    </div>

    <script>
        function showLoginPanel(panel) {
            document.getElementById('panel-password').style.display = panel === 'password' ? '' : 'none';
            document.getElementById('panel-code').style.display = panel === 'code' ? '' : 'none';
        }
    </script>
@endsection
