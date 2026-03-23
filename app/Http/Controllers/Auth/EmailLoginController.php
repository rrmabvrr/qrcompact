<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginCodeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailLoginController extends Controller
{
    private const CODE_EXPIRATION_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = mb_strtolower(trim($validated['email']));
        $user = User::query()->where('email', $email)->first();

        // Avoid user enumeration by keeping the same success message.
        if (! $user) {
            return back()->with('status', 'Se o email estiver cadastrado, enviaremos um codigo de acesso.');
        }

        $code = (string) random_int(100000, 999999);

        DB::table('email_login_codes')->where('email', $email)->delete();
        DB::table('email_login_codes')->insert([
            'email' => $email,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::CODE_EXPIRATION_MINUTES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($email)->send(new LoginCodeMail($code, self::CODE_EXPIRATION_MINUTES));

        return redirect()
            ->route('login.verify.form', ['email' => $email])
            ->with('status', 'Codigo enviado para seu email.');
    }

    public function showVerifyForm(Request $request): View
    {
        $email = (string) $request->query('email', old('email', ''));

        return view('auth.verify', [
            'email' => $email,
        ]);
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $email = mb_strtolower(trim($validated['email']));

        $loginCode = DB::table('email_login_codes')->where('email', $email)->first();

        if (! $loginCode) {
            return back()
                ->withInput(['email' => $email])
                ->withErrors(['code' => 'Codigo invalido ou expirado. Solicite um novo.']);
        }

        if (now()->greaterThan($loginCode->expires_at)) {
            DB::table('email_login_codes')->where('email', $email)->delete();

            return back()
                ->withInput(['email' => $email])
                ->withErrors(['code' => 'Codigo expirado. Solicite um novo.']);
        }

        if ((int) $loginCode->attempts >= self::MAX_ATTEMPTS) {
            DB::table('email_login_codes')->where('email', $email)->delete();

            return back()
                ->withInput(['email' => $email])
                ->withErrors(['code' => 'Muitas tentativas. Solicite um novo codigo.']);
        }

        if (! Hash::check($validated['code'], $loginCode->code_hash)) {
            DB::table('email_login_codes')
                ->where('email', $email)
                ->update([
                    'attempts' => (int) $loginCode->attempts + 1,
                    'updated_at' => now(),
                ]);

            return back()
                ->withInput(['email' => $email])
                ->withErrors(['code' => 'Codigo incorreto.']);
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            DB::table('email_login_codes')->where('email', $email)->delete();

            return redirect()->route('login')
                ->withErrors(['email' => 'Nao foi possivel autenticar este email.']);
        }

        DB::table('email_login_codes')->where('email', $email)->delete();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('links.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
