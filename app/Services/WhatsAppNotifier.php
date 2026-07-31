<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envía un aviso por WhatsApp al fotógrafo (a su propio número).
 * Usa CallMeBot (gratis). Se activa configurando WHATSAPP_NOTIFY_PHONE + CALLMEBOT_APIKEY.
 * Es tolerante a fallos: si no está configurado o el servicio falla, no rompe nada.
 */
class WhatsAppNotifier
{
    /**
     * Aviso cuando el cliente SUBE SU COMPROBANTE de Yape ("Enviar comprobante").
     * Es el momento en que el cliente ya pagó de verdad → el fotógrafo debe revisar y aprobar.
     */
    public function notifyReceiptUploaded(Order $order): void
    {
        $order->loadMissing('event');
        $cur   = $order->event?->currency ?: '';
        $total = number_format((float) $order->total, 2);
        $adminLink = route('admin.orders.show', ['order' => $order->id]);

        $text = "💰 ¡Comprobante recibido! El cliente ya pagó.\n"
              . "Pedido {$order->code}\n"
              . "Cliente: {$order->customer_name}\n"
              . "Fotos: {$order->photo_count} · Total: {$cur} {$total}\n"
              . "Contacto: {$order->customer_contact}\n"
              . ($order->op_code ? "Código de operación: {$order->op_code}\n" : '')
              . "Revisar y aprobar: {$adminLink}";

        $this->send($text);
    }

    /**
     * Aviso cuando entra un pedido nuevo (antes de pagar). Disponible pero NO se usa
     * por defecto: el fotógrafo prefiere enterarse sólo cuando el cliente ya pagó.
     */
    public function notifyNewOrder(Order $order): void
    {
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

        $this->send($text);
    }

    /** Envía el mensaje por CallMeBot. Silencioso si no está configurado o si falla. */
    private function send(string $text): void
    {
        $phone  = config('services.callmebot.phone');
        $apikey = config('services.callmebot.apikey');
        if (! $phone || ! $apikey) {
            return; // no configurado todavía
        }

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
