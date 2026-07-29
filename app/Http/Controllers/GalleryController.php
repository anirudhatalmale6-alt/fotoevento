<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
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
                Storage::disk(config('storage.private_disk'))->delete($order->receipt_path);
            }
            $order->receipt_path = $request->file('receipt')
                ->store("receipts/{$event->id}", config('storage.private_disk'));
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
        $disk  = Storage::disk(config('storage.private_disk'));
        abort_unless($photo && $disk->exists($photo->original_path), 404);

        $filename = ($item->code ?: 'foto') . '.jpg';
        return $disk->download($photo->original_path, $filename);
    }

    /** Acceso al pedido: por token en el enlace (?t=) o por sesión de compra. */
    private function authorizeOrder(Request $request, Order $order): void
    {
        $token = (string) $request->query('t', '');
        $bySession = (bool) session('order_ok_' . $order->id, false);
        $byToken   = filled($order->token) && hash_equals($order->token, $token);

        abort_unless($bySession || $byToken, 403);
    }

    /**
     * Registra una previsualización de foto (clic para ver en grande / lightbox).
     * Se llama por AJAX desde la galería. No guarda datos personales, sólo un id anónimo.
     */
    public function track(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->where('published', true)->first();
        if (! $event) {
            return response()->json(['ok' => false], 404);
        }

        $data = $request->validate([
            'type'     => ['required', 'in:photo_preview'],
            'photo_id' => ['nullable', 'integer'],
        ]);

        $vid = $this->visitorId();

        if ($data['type'] === 'photo_preview' && ! empty($data['photo_id'])) {
            $photoId = (int) $data['photo_id'];
            if ($event->photos()->whereKey($photoId)->exists()) {
                $key = "av:prev:{$event->id}:{$photoId}:{$vid}";
                if (! Cache::has($key)) {
                    Cache::put($key, 1, now()->addMinutes(30));
                    AnalyticsEvent::record(AnalyticsEvent::PREVIEW, $event->id, $photoId, $vid);
                }
            }
        }

        return response()->json(['ok' => true]);
    }

    private function gallery(Event $event)
    {
        $event->load(['photos', 'packages']);
        $this->logView($event);
        return view('gallery.show', compact('event'));
    }

    /** Cuenta una visita a la galería (una por visitante cada ~3h para no inflar). */
    private function logView(Event $event): void
    {
        $vid = $this->visitorId();
        $key = "av:view:{$event->id}:{$vid}";
        if (! Cache::has($key)) {
            Cache::put($key, 1, now()->addHours(3));
            AnalyticsEvent::record(AnalyticsEvent::VIEW, $event->id, null, $vid);
        }
    }

    /** Id anónimo del visitante (cookie de 1 año). No identifica a la persona. */
    private function visitorId(): string
    {
        $vid = (string) request()->cookie('fe_vid');
        if (! preg_match('/^[a-f0-9]{32}$/', $vid)) {
            $vid = bin2hex(random_bytes(16));
            Cookie::queue('fe_vid', $vid, 60 * 24 * 365);
        }
        return $vid;
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
