<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'links'])->name('links.index');
Route::get('/pix', [PageController::class, 'pix'])->name('pix.index');
Route::get('/whatsapp', [PageController::class, 'whatsapp'])->name('whatsapp.index');

Route::get('/{slug}', RedirectController::class)
    ->where('slug', '^(?!api$|pix$|whatsapp$|up$)[A-Za-z0-9]{6}$')
    ->name('links.redirect');
