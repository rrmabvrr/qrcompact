@extends('layouts.guest', ['title' => 'Validar código - QRCompact'])

@section('content')
@if (session('is_first_access'))
<h1 class="guest-title">Confirme seu e-mail</h1>
<p class="guest-subtitle">Enviamos um código de ativação para <strong>{{ $email }}</strong>. Insira abaixo para criar
    sua conta.</p>
@else
<h1 class="guest-title">Digite o código</h1>
<p class="guest-subtitle">Enviamos um código de 6 dígitos para <strong>{{ $email }}</strong>.</p>
@endif

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
        <label for="code" class="form-label">Código de 6 dígitos</label>
        <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
            class="form-control" placeholder="000000" required autofocus>
    </div>

    <div class="col-12 d-grid gap-2">
        <button type="submit" class="btn-generate w-100 justify-content-center">Entrar</button>
        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Alterar e-mail</a>
    </div>
</form>
@endsection