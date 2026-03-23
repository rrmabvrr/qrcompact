<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\Auth\EmailLoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [EmailLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [EmailLoginController::class, 'sendCode'])->name('login.send-code');
    Route::get('/login/verificar', [EmailLoginController::class, 'showVerifyForm'])->name('login.verify.form');
    Route::post('/login/verificar', [EmailLoginController::class, 'verifyCode'])->name('login.verify');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [PageController::class, 'links'])->name('links.index');
    Route::get('/pix', [PageController::class, 'pix'])->name('pix.index');
    Route::get('/whatsapp', [PageController::class, 'whatsapp'])->name('whatsapp.index');
    Route::post('/logout', [EmailLoginController::class, 'logout'])->name('logout');
});

Route::get('/{slug}', RedirectController::class)
    ->where('slug', '^(?!api$|pix$|whatsapp$|up$)[A-Za-z0-9]{6}$')
    ->name('links.redirect');
