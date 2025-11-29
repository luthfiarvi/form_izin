<?php

use App\Http\Controllers\FormIzinController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\GamificationController as AdminGamificationController;
use App\Http\Controllers\Admin\PolicyLogController as AdminPolicyLogController;
use App\Http\Controllers\Admin\IzinController as AdminIzinController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\FileServeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('izin.create');
    }
    return redirect()->route('login');
});

// After login, send users to our main feature page instead of the default dashboard
Route::get('/dashboard', function () {
    return redirect()->route('izin.create');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/signature', [ProfileController::class, 'updateSignature'])->name('profile.signature.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Pengajuan Izin (hanya untuk user yang telah disetujui)
    Route::middleware('approved')->group(function () {
        // Gracefully handle GET /izin by redirecting to the create form
        Route::get('/izin', function () {
            return redirect()->route('izin.create');
        })->name('izin');
        Route::get('/izin/create', [FormIzinController::class, 'create'])->name('izin.create');
        Route::post('/izin', [FormIzinController::class, 'store'])->name('izin.store');
        Route::get('/izin/data', [FormIzinController::class, 'index'])->name('izin.data');
        Route::get('/izin/{formIzin}/view', [FormIzinController::class, 'view'])->name('izin.view');

        // Ringkasan & riwayat poin pelanggaran
        Route::get('/points', [PointController::class, 'index'])->name('points.index');
    });
    // Admin-only actions for the data page
    Route::middleware('admin')->group(function () {
        Route::get('/izin/data/export', [FormIzinController::class, 'export'])->name('izin.data.export');
        Route::post('/izin/{formIzin}/approve', [FormIzinController::class, 'approve'])->name('izin.approve');
        Route::post('/izin/{formIzin}/reject', [FormIzinController::class, 'reject'])->name('izin.reject');
        Route::delete('/izin/{formIzin}', [FormIzinController::class, 'destroy'])->name('izin.delete');
    });

    // Protected storage routes
    Route::get('/files/attachments/{filename}', [FileServeController::class, 'serveAttachment'])
        ->where('filename', '[A-Za-z0-9_\.-]+')
        ->name('files.attachment');
    Route::get('/files/signatures/{filename}', [FileServeController::class, 'serveSignature'])
        ->where('filename', '[A-Za-z0-9_\.-]+')
        ->name('files.signature');
    Route::get('/files/head-signature/{formIzin}', [FileServeController::class, 'serveHeadSignature'])
        ->name('files.head_signature');
});

require __DIR__.'/auth.php';

// Admin routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // Izin management
    Route::get('/izin', [AdminIzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/export', [AdminIzinController::class, 'export'])->name('izin.export');
    Route::get('/izin/{formIzin}', [AdminIzinController::class, 'show'])->name('izin.show');
    Route::patch('/izin/{formIzin}', [AdminIzinController::class, 'update'])->name('izin.update');
    Route::delete('/izin/{formIzin}', [AdminIzinController::class, 'destroy'])->name('izin.destroy');

    // User management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');

    // Gamification settings & reports
    Route::get('/gamification/settings', [AdminGamificationController::class, 'edit'])->name('gamification.settings');
    Route::post('/gamification/settings', [AdminGamificationController::class, 'update'])->name('gamification.settings.update');
    Route::get('/gamification/summary', [AdminGamificationController::class, 'summary'])->name('gamification.summary');

    // Policy log viewer
    Route::get('/policy-log', [AdminPolicyLogController::class, 'index'])->name('policy-log.index');
});
