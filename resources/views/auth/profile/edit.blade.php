@extends('layouts.app', ['title' => 'Configuracoes do Perfil - QRCompact', 'layoutMode' => 'settings'])

@section('content')
<div class="settings-wrap">
    <div class="settings-card">
        <h1 class="settings-title">Configuracoes do perfil</h1>
        <p class="settings-subtitle">Atualize nome e email da sua conta.</p>
    </div>

    @if (session('status'))
    <div class="alert alert-success m-0" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger m-0" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="settings-card">
        <form action="{{ route('profile.update') }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')

            <div class="col-md-6">
                <label for="name" class="form-label">Nome</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Seu nome">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn-premium">Salvar alteracoes</button>
            </div>
        </form>
    </div>
</div>
@endsection
