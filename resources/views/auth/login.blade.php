@extends('layouts.guest', ['title' => 'Entrar - QRCompact'])

@section('content')
    <h1 class="guest-title">Entrar com email</h1>
    <p class="guest-subtitle">Informe seu email cadastrado para receber um codigo de 6 digitos.</p>

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
                placeholder="email@exemplo.com"
                required
                autofocus>
        </div>

        <div class="col-12">
            <button type="submit" class="btn-generate w-100 justify-content-center">
                Enviar codigo de acesso
            </button>
        </div>
    </form>
@endsection
