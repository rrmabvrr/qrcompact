@extends('layouts.guest', ['title' => 'Validar codigo - QRCompact'])

@section('content')
    <h1 class="guest-title">Digite o codigo</h1>
    <p class="guest-subtitle">Enviamos um codigo de 6 digitos para <strong>{{ $email }}</strong>.</p>

    @if (session('status'))
        <div class="alert alert-info" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('login.verify') }}" method="POST" class="row g-3 mt-1">
        @csrf
        <input type="hidden" name="email" value="{{ old('email', $email) }}">

        <div class="col-12">
            <label for="code" class="form-label">Codigo de 6 digitos</label>
            <input
                id="code"
                name="code"
                type="text"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                class="form-control"
                placeholder="000000"
                required
                autofocus>
        </div>

        <div class="col-12 d-grid gap-2">
            <button type="submit" class="btn-generate w-100 justify-content-center">Entrar</button>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary">Alterar email</a>
        </div>
    </form>
@endsection
