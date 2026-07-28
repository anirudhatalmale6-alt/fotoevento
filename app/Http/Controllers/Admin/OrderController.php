<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
}
