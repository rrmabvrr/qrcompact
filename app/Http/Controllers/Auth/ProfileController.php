<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('auth.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $emailChanged = $validated['email'] !== $user->email;

        $user->fill([
            'name' => $validated['name'] ?: null,
            'email' => mb_strtolower(trim($validated['email'])),
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('status', 'Perfil atualizado com sucesso.');
    }

    public function editPassword(): View
    {
        return view('auth.profile.password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! $user->password || ! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'A senha atual esta incorreta.',
            ]);
        }

        $user->update([
            'password' => $validated['password'],
        ]);

        return back()->with('status', 'Senha atualizada com sucesso.');
    }
}
