<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RealtimeController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

// ── Public share view (no auth required) ──────────────────────────
Route::get('/s/{token}', [DocumentController::class, 'publicView'])->name('documents.public');

// ── Guest routes ───────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ── Authenticated routes ───────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Onboarding
    Route::get('/welcome', fn () => view('welcome-onboard'))->name('onboarding');

    // Dashboard
    Route::get('/dashboard', [DocumentController::class, 'index'])->name('dashboard');

    // Documents — creation
    Route::post('/documents',                   [DocumentController::class, 'store'])->name('documents.store');
    Route::post('/documents/from-template',     [DocumentController::class, 'storeFromTemplate'])->name('documents.from-template');

    // Documents — view / edit
    Route::get('/documents/{slug}/edit',        [DocumentController::class, 'edit'])->name('documents.edit');
    Route::get('/documents/{slug}/export',      [DocumentController::class, 'export'])->name('documents.export');

    // Documents — AJAX updates
    Route::patch('/documents/{slug}',           [DocumentController::class, 'update'])->name('documents.update');
    Route::patch('/documents/{slug}/rename',    [DocumentController::class, 'rename'])->name('documents.rename');
    Route::patch('/documents/{slug}/star',      [DocumentController::class, 'toggleStar'])->name('documents.star');
    Route::patch('/documents/{slug}/folder',    [DocumentController::class, 'moveFolder'])->name('documents.folder');
    Route::patch('/documents/{slug}/tags',      [DocumentController::class, 'updateTags'])->name('documents.tags');
    Route::patch('/documents/{slug}/public',    [DocumentController::class, 'togglePublic'])->name('documents.public-toggle');

    // Documents — actions
    Route::post('/documents/{slug}/duplicate',  [DocumentController::class, 'duplicate'])->name('documents.duplicate');
    Route::post('/documents/upload-image',      [DocumentController::class, 'uploadImage'])->name('documents.upload-image');
    Route::delete('/documents/{slug}',          [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Version history
    Route::get('/documents/{slug}/versions',                        [DocumentController::class, 'versions'])->name('documents.versions');
    Route::post('/documents/{slug}/versions/{id}/restore',          [DocumentController::class, 'restoreVersion'])->name('documents.versions.restore');

    // Real-time presence (legacy polling)
    Route::post('/documents/{slug}/presence/heartbeat', [PresenceController::class, 'heartbeat'])->name('documents.presence.heartbeat');
    Route::post('/documents/{slug}/presence/leave',     [PresenceController::class, 'leave'])->name('documents.presence.leave');

    // Real-time sync (fast — cache based, 300ms polling)
    Route::post('/documents/{slug}/rt/push', [RealtimeController::class, 'push'])->name('documents.rt.push');
    Route::post('/documents/{slug}/rt/pull', [RealtimeController::class, 'pull'])->name('documents.rt.pull');
    Route::post('/documents/{slug}/rt/leave',[RealtimeController::class, 'leave'])->name('documents.rt.leave');

    // Profile
    Route::get('/profile',             [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/info',      [ProfileController::class, 'updateInfo'])->name('profile.update-info');
    Route::patch('/profile/password',  [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Teams
    Route::get('/teams',                                [TeamController::class, 'index'])->name('teams.index');
    Route::post('/teams',                               [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{slug}',                         [TeamController::class, 'show'])->name('teams.show');
    Route::get('/teams/{slug}/settings',                [TeamController::class, 'settings'])->name('teams.settings');
    Route::patch('/teams/{slug}',                       [TeamController::class, 'update'])->name('teams.update');
    Route::post('/teams/{slug}/invite',                 [TeamController::class, 'invite'])->name('teams.invite');
    Route::patch('/teams/{slug}/members/{user}/role',   [TeamController::class, 'updateRole'])->name('teams.update-role');
    Route::delete('/teams/{slug}/members/{user}',       [TeamController::class, 'removeMember'])->name('teams.remove-member');
    Route::post('/teams/{slug}/leave',                  [TeamController::class, 'leave'])->name('teams.leave');
    Route::delete('/teams/{slug}',                      [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::post('/teams/{slug}/documents',              [TeamController::class, 'createDocument'])->name('teams.create-document');
    Route::post('/teams/{slug}/assign-document',        [TeamController::class, 'assignDocument'])->name('teams.assign-document');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
