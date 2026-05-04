<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginCodeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showRequestForm(): View
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $email = mb_strtolower(trim($request->email));

        // Se o e-mail não estiver cadastrado, cria a conta e envia código de acesso
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            User::create(['email' => $email]);

            $code = (string) random_int(100000, 999999);

            DB::table('email_login_codes')->where('email', $email)->delete();
            DB::table('email_login_codes')->insert([
                'email'      => $email,
                'code_hash'  => Hash::make($code),
                'attempts'   => 0,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Mail::to($email)->send(new LoginCodeMail($code, 10, true));

            return redirect()
                ->route('login.verify.form', ['email' => $email])
                ->with('status', 'Email nao cadastrado. Criamos sua conta e enviamos um codigo de acesso para ' . $email . '.');
        }

        $status = Password::sendResetLink(['email' => $email]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token = null): View
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
