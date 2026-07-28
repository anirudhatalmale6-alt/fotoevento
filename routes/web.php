<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;

// Landing simple -> panel
Route::get('/', fn () => redirect()->route('admin.events.index'));

/* ---------- Admin ---------- */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('events.index');
        Route::get('eventos/nuevo', [EventController::class, 'create'])->name('events.create');
        Route::post('eventos', [EventController::class, 'store'])->name('events.store');
        Route::get('eventos/{event}', [EventController::class, 'show'])->name('events.show');
        Route::put('eventos/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('eventos/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        Route::post('eventos/{event}/fotos', [EventController::class, 'uploadPhotos'])->name('events.photos.upload');
        Route::delete('eventos/{event}/fotos/{photo}', [EventController::class, 'destroyPhoto'])->name('events.photos.destroy');
    });
});

/* ---------- Galería pública (por enlace + PIN) ---------- */
Route::get('/g/{slug}', [GalleryController::class, 'show'])->name('gallery.show');
Route::post('/g/{slug}/acceso', [GalleryController::class, 'unlock'])->name('gallery.unlock');
