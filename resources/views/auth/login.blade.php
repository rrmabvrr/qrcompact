@extends('layouts.guest', ['title' => 'Entrar - QRCompact'])

@section('content')

@if (session('status'))
<div class="alert alert-info" role="status">{{ session('status') }}</div>
@endif

@if ($errors->any())
<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
@endif

{{-- Painel: login por senha --}}
<div id="panel-password" class="login-panel" @if(old('_panel') !=='password' ) style="display:none" @endif>
    <h1 class="guest-title">Entrar com senha</h1>
    <p class="guest-subtitle">Use seu e-mail e senha para acessar sua conta.</p>

    <form action="{{ route('login.password') }}" method="POST" class="row g-3">
        @csrf
        <input type="hidden" name="_panel" value="password">

        <div class="col-12">
            <label for="email-pass" class="form-label">E-mail</label>
            <input id="email-pass" name="email" type="email" value="{{ old('email') }}" class="form-control" placeholder="email@exemplo.com" required autofocus>
        </div>

        <div class="col-12">
            <label for="password" class="form-label">Senha</label>
            <input id="password" name="password" type="password" class="form-control" placeholder="" required>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="toggle-login-password">
                <label class="form-check-label" for="toggle-login-password">Mostrar senha</label>
            </div>
            <div class="text-end mt-1">
                <a href="{{ route('password.request') }}" class="small text-decoration-none">Esqueci minha senha /
                    Recuperar conta</a>
            </div>
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
        <button type="button" class="btn btn-secondary btn-link-subtle" onclick="showLoginPanel('register')">
            <i class="bi bi-person-plus me-1"></i>Nao tenho cadastro
        </button>
    </div>
</div>

{{-- Painel: cadastro por senha --}}
<div id="panel-register" class="login-panel" @if(old('_panel') !=='register' ) style="display:none" @endif>
    <h1 class="guest-title">Cadastro</h1>
    <p class="guest-subtitle">Crie sua conta com e-mail e senha.</p>

    <form action="{{ route('register.password') }}" method="POST" class="row g-3">
        @csrf
        <input type="hidden" name="_panel" value="register">

        <div class="col-12">
            <label for="register-email" class="form-label">E-mail</label>
            <input id="register-email" name="email" type="email" value="{{ old('email') }}" class="form-control" placeholder="email@exemplo.com" required autofocus>
        </div>

        <div class="col-12">
            <label for="register-password" class="form-label">Senha</label>
            <input id="register-password" name="password" type="password" class="form-control" required>
        </div>

        <div class="col-12">
            <label for="register-password_confirmation" class="form-label">Confirmar senha</label>
            <input id="register-password_confirmation" name="password_confirmation" type="password" class="form-control" required>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="toggle-register-password">
                <label class="form-check-label" for="toggle-register-password">Mostrar senhas</label>
            </div>
        </div>

        <div class="col-12">
            <button type="submit" class="btn-generate w-100 justify-content-center">
                Criar conta
            </button>
        </div>
    </form>

    <div class="text-center mt-3">
        <button type="button" class="btn btn-secondary btn-link-subtle" onclick="showLoginPanel('password')">
            <i class="bi bi-key me-1"></i>Já tenho cadastro
        </button>
    </div>
</div>

{{-- Painel: login por código (padrão) --}}
<div id="panel-code" class="login-panel" @if(old('_panel')==='password' || old('_panel')==='register' ) style="display:none" @endif>
    <h1 class="guest-title">Entrar com código</h1>
    <p class="guest-subtitle">Receba um código por e-mail para acessar sua conta.</p>

    <button type="button" class="btn btn-outline-secondary btn-outline-login w-100 mt-1" onclick="showLoginPanel('password')">
        <u class="me-2">***</u> Acessar com senha
    </button>

    <button type="button" class="btn btn-secondary btn-link-subtle w-100 mt-2" onclick="showLoginPanel('register')">
        <i class="bi bi-person-plus me-1"></i>Criar conta de usuário.
    </button>

    <div class="login-or my-3">
        <span>Ou acesse recebendo um<br>código de acesso por e-mail</span>
    </div>

    <form action="{{ route('login.send-code') }}" method="POST" class="row g-3">
        @csrf
        <input type="hidden" name="_panel" value="code">

        <div class="col-12">
            <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control" placeholder="email@exemplo.com" required autofocus>
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
        document.getElementById('panel-register').style.display = panel === 'register' ? '' : 'none';
        document.getElementById('panel-code').style.display = panel === 'code' ? '' : 'none';
    }

    const loginPasswordInput = document.getElementById('password');
    const loginTogglePassword = document.getElementById('toggle-login-password');
    if (loginPasswordInput && loginTogglePassword) {
        loginTogglePassword.addEventListener('change', function() {
            loginPasswordInput.type = this.checked ? 'text' : 'password';
        });
    }

    const registerPasswordInput = document.getElementById('register-password');
    const registerPasswordConfirmationInput = document.getElementById('register-password_confirmation');
    const registerTogglePassword = document.getElementById('toggle-register-password');
    if (registerPasswordInput && registerPasswordConfirmationInput && registerTogglePassword) {
        registerTogglePassword.addEventListener('change', function() {
            const type = this.checked ? 'text' : 'password';
            registerPasswordInput.type = type;
            registerPasswordConfirmationInput.type = type;
        });
    }

</script>
@endsection
