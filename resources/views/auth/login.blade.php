@extends('layouts.guest', ['title' => 'Entrar - QRCompact'])

@section('content')
    <h1 class="guest-title">Acesse sua conta</h1>
    <p class="guest-subtitle">Novo por aqui? Basta informar seu email — criaremos sua conta e enviaremos um codigo de acesso automaticamente.</p>

    @if (session('status'))
        <div class="alert alert-info" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('login.send-code') }}" method="POST" class="row g-3 mt-1">
        @csrf
        <div class="col-12">
            <label for="email" class="form-label">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                class="form-control"
                placeholder="voce@exemplo.com"
                required
                autofocus>
        </div>

        <div class="col-12">
            <button type="submit" class="btn-generate w-100 justify-content-center">
                Continuar com email
            </button>
        </div>
    </form>
@endsection
