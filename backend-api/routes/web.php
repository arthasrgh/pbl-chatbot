<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\ChatController;

Route::get('/', function () {
    return view('welcome');
});

// =========================
// DASHBOARD FAQ
// =========================

// Tampil semua FAQ
Route::get('/admin/faqs', [FaqController::class, 'index']);

// Form tambah FAQ
Route::get('/admin/faqs/create', [FaqController::class, 'create']);

// Simpan FAQ baru
Route::post('/admin/faqs', [FaqController::class, 'store']);

// Form edit FAQ
Route::get('/admin/faqs/{id}/edit', [FaqController::class, 'edit']);

// Update FAQ
Route::put('/admin/faqs/{id}', [FaqController::class, 'update']);

// Hapus FAQ
Route::delete('/admin/faqs/{id}', [FaqController::class, 'destroy']);

Route::get(
    '/admin/chats',
    [ChatController::class, 'index']
);

Route::get(
    '/admin/chats/{nomor}',
    [ChatController::class, 'show']
);