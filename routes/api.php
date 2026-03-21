<?php

use App\Http\Controllers\Api\LinkController;
use App\Http\Controllers\Api\QrCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/links', [LinkController::class, 'index'])->name('api.links.index');
Route::post('/shorten', [LinkController::class, 'store'])->name('api.links.store');
Route::get('/links/{slug}', [LinkController::class, 'show'])
    ->where('slug', '[A-Za-z0-9]{6}')
    ->name('api.links.show');
Route::put('/links/{slug}', [LinkController::class, 'update'])
    ->where('slug', '[A-Za-z0-9]{6}')
    ->name('api.links.update');
Route::post('/qr', QrCodeController::class)->name('api.qr.store');
