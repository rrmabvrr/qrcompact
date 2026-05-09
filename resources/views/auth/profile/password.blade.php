@extends('layouts.app', ['title' => 'Mudanca de Senha - QRCompact', 'layoutMode' => 'settings'])

@section('content')
<div class="page-grid">
    <div class="settings-wrap">
        <div class="settings-card">
            <h1 class="settings-title">Mudanca de senha</h1>
            <p class="settings-subtitle">Informe sua senha atual e escolha uma nova senha.</p>

            @if (session('status'))
            <div class="alert alert-success mt-3 mb-0" role="status">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger mt-3 mb-0" role="alert">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('profile.password.update') }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label for="current_password" class="form-label">Senha atual</label>
                    <input id="current_password" name="current_password" type="password"
                        class="form-control @error('current_password') is-invalid @enderror" required>
                    @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">Nova senha</label>
                    <input id="password" name="password" type="password"
                        class="form-control @error('password') is-invalid @enderror" minlength="8" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirmar nova senha</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                        minlength="8" required>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="toggle-profile-passwords">
                        <label class="form-check-label" for="toggle-profile-passwords">Mostrar senhas</label>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end pt-1">
                    <button type="submit" class="btn-premium">Atualizar senha</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const profilePasswordInputs = [
        document.getElementById('current_password'),
        document.getElementById('password'),
        document.getElementById('password_confirmation')
    ].filter(Boolean);

    const profileTogglePasswords = document.getElementById('toggle-profile-passwords');
    if (profileTogglePasswords && profilePasswordInputs.length) {
        profileTogglePasswords.addEventListener('change', function() {
            const inputType = this.checked ? 'text' : 'password';
            profilePasswordInputs.forEach(function(input) {
                input.type = inputType;
            });
        });
    }
</script>
@endsection