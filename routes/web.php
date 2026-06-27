<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DocumentController::class, 'index'])->name('dashboard');

    // Documents
    Route::post('/documents',                       [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{slug}/edit',            [DocumentController::class, 'edit'])->name('documents.edit');
    Route::get('/documents/{slug}/export',          [DocumentController::class, 'export'])->name('documents.export');
    Route::patch('/documents/{slug}',               [DocumentController::class, 'update'])->name('documents.update');
    Route::patch('/documents/{slug}/rename',        [DocumentController::class, 'rename'])->name('documents.rename');
    Route::patch('/documents/{slug}/star',          [DocumentController::class, 'toggleStar'])->name('documents.star');
    Route::patch('/documents/{slug}/folder',        [DocumentController::class, 'moveFolder'])->name('documents.folder');
    Route::post('/documents/{slug}/duplicate',      [DocumentController::class, 'duplicate'])->name('documents.duplicate');
    Route::delete('/documents/{slug}',              [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Profile
    Route::get('/profile',              [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/info',       [ProfileController::class, 'updateInfo'])->name('profile.update-info');
    Route::patch('/profile/password',   [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
