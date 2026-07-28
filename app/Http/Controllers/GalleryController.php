<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    // Página de acceso (pide PIN si el evento tiene uno)
    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)->where('published', true)->firstOrFail();

        if (blank($event->pin) || $this->unlocked($event)) {
            return $this->gallery($event);
        }
        return view('gallery.pin', ['event' => $event, 'error' => null]);
    }

    public function unlock(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->where('published', true)->firstOrFail();
        $pin = (string) $request->input('pin');

        if (blank($event->pin) || hash_equals($event->pin, $pin)) {
            $request->session()->put($this->key($event), true);
            return redirect()->route('gallery.show', $event->slug);
        }
        return view('gallery.pin', ['event' => $event, 'error' => 'PIN incorrecto. Inténtalo de nuevo.']);
    }

    /**
     * El cliente confirma su selección. Se recalcula el precio en el servidor
     * (fuente de verdad) y se registra el pedido en estado "pendiente".
     */
    public function storeOrder(Request $request, string $slug, PricingService $pricing)
    {
        $event = Event::where('slug', $slug)->where('published', true)->firstOrFail();

        if (filled($event->pin) && ! $this->unlocked($event)) {
            abort(403);
        }

        $data = $request->validate([
            'photo_ids'        => ['required', 'array', 'min:1'],
            'photo_ids.*'      => ['integer'],
            'customer_name'    => ['required', 'string', 'max:120'],
            'customer_contact' => ['required', 'string', 'max:60'],
            'customer_email'   => ['nullable', 'email', 'max:120'],
        ], [], [
            'photo_ids'        => 'fotos',
            'customer_name'    => 'nombre',
            'customer_contact' => 'WhatsApp / celular',
        ]);

        $photos = $event->photos()
            ->whereIn('id', $data['photo_ids'])
            ->get(['id', 'code']);

        if ($photos->isEmpty()) {
            return back()->withErrors(['photo_ids' => 'Selecciona al menos una foto válida.']);
        }

        $quote = $pricing->quote($event, $photos->count());
        $unit  = (float) $event->price_unit;

        $order = DB::transaction(function () use ($event, $data, $photos, $quote, $unit) {
            $order = Order::create([
                'event_id'         => $event->id,
                'code'             => Order::makeCode(),
                'token'            => Order::makeToken(),
                'customer_name'    => $data['customer_name'],
                'customer_contact' => $data['customer_contact'],
                'customer_email'   => $data['customer_email'] ?? null,
                'photo_count'      => $photos->count(),
                'subtotal'         => $quote['sub'],
                'total'            => $quote['total'],
                'applied_label'    => $quote['applied_label'],
                'status'           => 'pendiente',
            ]);

            foreach ($photos as $photo) {
                $order->items()->create([
                    'photo_id'   => $photo->id,
                    'code'       => $photo->code,
                    'unit_price' => $unit,
                ]);
            }

            return $order;
        });

        $request->session()->put('order_ok_' . $order->id, true);

        return redirect()->route('gallery.order', [
            'slug' => $event->slug, 'code' => $order->code, 't' => $order->token,
        ]);
    }

    /** Pantalla del pedido: pago con Yape, subir comprobante o descargar (según estado). */
    public function order(Request $request, string $slug, string $code)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $order = Order::where('event_id', $event->id)->where('code', $code)->firstOrFail();

        $this->authorizeOrder($request, $order);

        $order->load('items.photo');
        $yape = Setting::yape();

        return view('gallery.order', compact('event', 'order', 'yape'));
    }

    /** El cliente sube su comprobante de Yape (captura) y/o el código de operación. */
    public function uploadReceipt(Request $request, string $slug, string $code)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $order = Order::where('event_id', $event->id)->where('code', $code)->firstOrFail();

        $this->authorizeOrder($request, $order);

        if ($order->isApproved()) {
            return redirect()->route('gallery.order', ['slug' => $slug, 'code' => $code, 't' => $order->token]);
        }

        $request->validate([
            'receipt' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:8192'],
            'op_code' => ['nullable', 'string', 'max:40'],
        ], [], ['receipt' => 'comprobante', 'op_code' => 'código de operación']);

        if (! $request->hasFile('receipt') && blank($request->input('op_code'))) {
            return back()->withErrors(['receipt' => 'Sube la captura del Yape o ingresa el código de operación.']);
        }

        // El comprobante se guarda en disco PRIVADO (sólo lo ve el fotógrafo desde su panel).
        if ($request->hasFile('receipt')) {
            if ($order->receipt_path) {
                Storage::disk('local')->delete($order->receipt_path);
            }
            $order->receipt_path = $request->file('receipt')
                ->store("receipts/{$event->id}", 'local');
        }

        $order->op_code = $request->input('op_code');
        $order->status  = 'comprobante';
        $order->paid_at = now();
        $order->save();

        return redirect()
            ->route('gallery.order', ['slug' => $slug, 'code' => $code, 't' => $order->token])
            ->with('flash', 'comprobante');
    }

    /** Descarga segura del ORIGINAL (sin marca de agua). Sólo si el pedido está aprobado. */
    public function download(Request $request, string $slug, string $code, OrderItem $item)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $order = Order::where('event_id', $event->id)->where('code', $code)->firstOrFail();

        $this->authorizeOrder($request, $order);
        abort_unless($order->isApproved(), 403, 'El pago aún no está aprobado.');
        abort_unless($item->order_id === $order->id, 404);

        $photo = $item->photo;
        abort_unless($photo && Storage::disk('local')->exists($photo->original_path), 404);

        $filename = ($item->code ?: 'foto') . '.jpg';
        return Storage::disk('local')->download($photo->original_path, $filename);
    }

    /** Acceso al pedido: por token en el enlace (?t=) o por sesión de compra. */
    private function authorizeOrder(Request $request, Order $order): void
    {
        $token = (string) $request->query('t', '');
        $bySession = (bool) session('order_ok_' . $order->id, false);
        $byToken   = filled($order->token) && hash_equals($order->token, $token);

        abort_unless($bySession || $byToken, 403);
    }

    private function gallery(Event $event)
    {
        $event->load(['photos', 'packages']);
        return view('gallery.show', compact('event'));
    }

    private function unlocked(Event $event): bool
    {
        return (bool) session($this->key($event), false);
    }

    private function key(Event $event): string
    {
        return 'gallery_ok_' . $event->id;
    }
}
