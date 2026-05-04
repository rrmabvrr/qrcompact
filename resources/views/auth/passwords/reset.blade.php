@extends('layouts.guest', ['title' => 'Redefinir Senha - QRCompact'])

@section('content')
    <h1 class="guest-title">Nova Senha</h1>
    <p class="guest-subtitle">Crie uma nova senha para sua conta.</p>

    <form action="{{ route('password.update') }}" method="POST" class="row g-3">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="col-12">
            <label for="email" class="form-label">Email</label>
            <input 
                id="email" 
                name="email" 
                type="email" 
                value="{{ $email ?? old('email') }}" 
                class="form-control @error('email') is-invalid @enderror" 
                placeholder="email@exemplo.com" 
                required 
                readonly>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label for="password" class="form-label">Nova Senha</label>
            <input 
                id="password" 
                name="password" 
                type="password" 
                class="form-control @error('password') is-invalid @enderror" 
                required 
                autofocus>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
            <input 
                id="password_confirmation" 
                name="password_confirmation" 
                type="password" 
                class="form-control" 
                required>
        </div>

        <div class="col-12">
            <button type="submit" class="btn-generate w-100 justify-content-center">
                Redefinir Senha
            </button>
        </div>
    </form>
@endsection
