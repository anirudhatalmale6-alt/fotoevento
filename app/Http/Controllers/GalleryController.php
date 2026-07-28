<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller
{
    // Página de acceso (pide PIN si el evento tiene uno)
    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)->where('published', true)->firstOrFail();

        // sin PIN -> acceso directo
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
     * El cliente confirma su selección de fotos. Se recalcula el precio en el
     * servidor (fuente de verdad) y se registra el pedido en estado "pendiente".
     * El pago con Yape y la aprobación se agregan en el Hito 3.
     */
    public function storeOrder(Request $request, string $slug, PricingService $pricing)
    {
        $event = Event::where('slug', $slug)->where('published', true)->firstOrFail();

        // el PIN debe estar validado en esta sesión (si el evento tiene PIN)
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

        // sólo fotos que realmente pertenecen a este evento (evita manipulación)
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

        // permitir ver la confirmación de este pedido en esta sesión
        $request->session()->put('order_ok_' . $order->id, true);

        return redirect()->route('gallery.order', ['slug' => $event->slug, 'code' => $order->code]);
    }

    /** Resumen del pedido (confirmación para el cliente). */
    public function order(Request $request, string $slug, string $code)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $order = Order::where('event_id', $event->id)->where('code', $code)->firstOrFail();

        abort_unless(session('order_ok_' . $order->id, false), 403);

        $order->load('items');
        return view('gallery.order', compact('event', 'order'));
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
