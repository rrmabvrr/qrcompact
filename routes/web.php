<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\Auth\EmailLoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [EmailLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [EmailLoginController::class, 'sendCode'])->name('login.send-code');
    Route::post('/login/senha', [EmailLoginController::class, 'loginWithPassword'])->name('login.password');
    Route::post('/cadastro/senha', [EmailLoginController::class, 'registerWithPassword'])->name('register.password');
    Route::get('/login/verificar', [EmailLoginController::class, 'showVerifyForm'])->name('login.verify.form');
    Route::post('/login/verificar', [EmailLoginController::class, 'verifyCode'])->name('login.verify');

    // Recuperação de Senha
    Route::get('/esqueci-senha', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
    Route::post('/esqueci-senha', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/redefinir-senha/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/redefinir-senha', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [PageController::class, 'links'])->name('links.index');
    Route::get('/pix', [PageController::class, 'pix'])->name('pix.index');
    Route::get('/whatsapp', [PageController::class, 'whatsapp'])->name('whatsapp.index');
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/perfil/senha', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/perfil/senha', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/logout', [EmailLoginController::class, 'logout'])->name('logout');
});

Route::get('/{slug}', RedirectController::class)
    ->where('slug', '^(?!api$|pix$|whatsapp$|up$)[A-Za-z0-9]{6}$')
    ->name('links.redirect');
