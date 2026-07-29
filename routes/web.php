<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SettingsController;
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

        // Pedidos (selección de clientes) + pago Yape con aprobación manual.
        Route::get('pedidos', [OrderController::class, 'index'])->name('orders.index');
        Route::delete('pedidos', [OrderController::class, 'destroyAll'])->name('orders.destroyAll');
        Route::get('pedidos/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('pedidos/{order}/comprobante', [OrderController::class, 'receipt'])->name('orders.receipt');
        Route::post('pedidos/{order}/aprobar', [OrderController::class, 'approve'])->name('orders.approve');
        Route::post('pedidos/{order}/rechazar', [OrderController::class, 'reject'])->name('orders.reject');
        Route::delete('pedidos/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

        // Configuración (datos de Yape)
        Route::get('configuracion', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::post('configuracion', [SettingsController::class, 'update'])->name('settings.update');

        // Seguridad: cambio de contraseña del administrador
        Route::post('password', [AuthController::class, 'updatePassword'])->name('password.update');
    });
});

/* ---------- Galería pública (por enlace + PIN) ---------- */
Route::get('/g/{slug}', [GalleryController::class, 'show'])->name('gallery.show');
Route::post('/g/{slug}/acceso', [GalleryController::class, 'unlock'])->name('gallery.unlock');
Route::post('/g/{slug}/pedido', [GalleryController::class, 'storeOrder'])->name('gallery.order.store');
Route::get('/g/{slug}/pedido/{code}', [GalleryController::class, 'order'])->name('gallery.order');
Route::post('/g/{slug}/pedido/{code}/comprobante', [GalleryController::class, 'uploadReceipt'])->name('gallery.order.receipt');
Route::get('/g/{slug}/pedido/{code}/descargar/{item}', [GalleryController::class, 'download'])->name('gallery.download');
