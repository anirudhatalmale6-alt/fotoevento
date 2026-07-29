<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envía un aviso por WhatsApp al fotógrafo (a su propio número) cuando entra un pedido.
 * Usa CallMeBot (gratis). Se activa configurando WHATSAPP_NOTIFY_PHONE + CALLMEBOT_APIKEY.
 * Es tolerante a fallos: si no está configurado o el servicio falla, no rompe nada.
 */
class WhatsAppNotifier
{
    public function notifyNewOrder(Order $order): void
    {
        $phone  = config('services.callmebot.phone');
        $apikey = config('services.callmebot.apikey');
        if (! $phone || ! $apikey) {
            return; // no configurado todavía
        }

        $order->loadMissing('event');
        $cur   = $order->event?->currency ?: '';
        $total = number_format((float) $order->total, 2);
        $link  = route('gallery.order', [
            'slug' => $order->event?->slug,
            'code' => $order->code,
            't'    => $order->token,
        ]);

        $text = "🎉 Nuevo pedido {$order->code}\n"
              . "Cliente: {$order->customer_name}\n"
              . "Fotos: {$order->photo_count} · Total: {$cur} {$total}\n"
              . "Contacto: {$order->customer_contact}\n"
              . "Ver pedido: {$link}";

        try {
            $res = Http::timeout(12)->get('https://api.callmebot.com/whatsapp.php', [
                'phone'  => $phone,
                'text'   => $text,
                'apikey' => $apikey,
            ]);
            if (! $res->successful()) {
                Log::warning('CallMeBot WhatsApp devolvió estado '.$res->status().': '.$res->body());
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar el aviso de WhatsApp: '.$e->getMessage());
        }
    }
}
