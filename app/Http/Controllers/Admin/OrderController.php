<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('event')->latest()->paginate(30);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['event', 'items.photo']);
        return view('admin.orders.show', compact('order'));
    }

    /** Muestra el comprobante de Yape (guardado en disco privado). */
    public function receipt(Order $order)
    {
        abort_unless($order->receipt_path && Storage::disk('local')->exists($order->receipt_path), 404);
        return response()->file(Storage::disk('local')->path($order->receipt_path));
    }

    /** El fotógrafo confirma el pago -> habilita la descarga en alta. */
    public function approve(Order $order)
    {
        $order->update([
            'status'      => 'aprobado',
            'approved_at' => now(),
        ]);

        return back()->with('ok', "Pedido {$order->code} aprobado. El cliente ya puede descargar sus fotos en alta.");
    }

    /** El comprobante no es válido -> el cliente puede reintentar. */
    public function reject(Order $order)
    {
        $order->update(['status' => 'rechazado']);
        return back()->with('ok', "Pedido {$order->code} marcado como rechazado. El cliente puede volver a enviar su comprobante.");
    }

    /** Elimina un pedido (y su comprobante). Las fotos originales del evento NO se tocan. */
    public function destroy(Order $order)
    {
        $code = $order->code;
        $this->deleteReceipt($order);
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('ok', "Pedido {$code} eliminado.");
    }

    /** Borra TODOS los pedidos y reinicia la numeración en FE-0001. Ideal para limpiar pruebas. */
    public function destroyAll()
    {
        foreach (Order::whereNotNull('receipt_path')->get() as $o) {
            $this->deleteReceipt($o);
        }

        DB::table('order_items')->delete();
        DB::table('orders')->delete();

        // Reiniciar el autoincremento para que la numeración vuelva a empezar en FE-0001.
        DB::statement('ALTER TABLE orders AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE order_items AUTO_INCREMENT = 1');

        return redirect()->route('admin.orders.index')
            ->with('ok', 'Se eliminaron todos los pedidos. La numeración vuelve a empezar en FE-0001.');
    }

    /** Elimina del disco privado el comprobante asociado a un pedido, si existe. */
    private function deleteReceipt(Order $order): void
    {
        if ($order->receipt_path && Storage::disk('local')->exists($order->receipt_path)) {
            Storage::disk('local')->delete($order->receipt_path);
        }
    }
}
