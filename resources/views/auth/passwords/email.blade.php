@extends('layouts.guest', ['title' => 'Recuperar Senha - QRCompact'])

@section('content')
<h1 class="guest-title">Recuperar Conta</h1>
<p class="guest-subtitle">Informe seu e-mail. Se já tiver cadastro, enviaremos um link para redefinir sua senha. Caso contrário, criaremos sua conta automaticamente e enviaremos um código de acesso.</p>

@if (session('status'))
<div class="alert alert-success" role="status">
    {{ session('status') }}
</div>
@endif

<form action="{{ route('password.email') }}" method="POST" class="row g-3">
    @csrf
    <div class="col-12">
        <label for="email" class="form-label">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="email@exemplo.com" required autofocus>
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <button type="submit" class="btn-generate w-100 justify-content-center">
            Enviar Link de Recuperação
        </button>
    </div>
</form>

<div class="text-center mt-3">
    <a href="{{ route('login') }}" class="btn btn-link-subtle">
        <i class="bi bi-arrow-left me-1"></i>Voltar ao login
    </a>
</div>
@endsection
