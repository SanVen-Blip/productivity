<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DocumentController::class, 'index'])->name('dashboard');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{slug}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::patch('/documents/{slug}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{slug}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
