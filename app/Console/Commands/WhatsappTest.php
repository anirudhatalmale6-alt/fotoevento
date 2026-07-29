<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class WhatsappTest extends Command
{
    protected $signature = 'whatsapp:test';

    protected $description = 'Envía un WhatsApp de prueba al fotógrafo (verifica CallMeBot)';

    public function handle(): int
    {
        $phone  = config('services.callmebot.phone');
        $apikey = config('services.callmebot.apikey');

        if (! $phone || ! $apikey) {
            $this->error('Falta configurar WHATSAPP_NOTIFY_PHONE y/o CALLMEBOT_APIKEY en .env');
            return self::FAILURE;
        }

        $text = "✅ Prueba de FotoEvento: los avisos por WhatsApp están funcionando. "
              . "Recibirás un mensaje así cada vez que entre un pedido.";

        try {
            $res = Http::timeout(15)->get('https://api.callmebot.com/whatsapp.php', [
                'phone' => $phone, 'text' => $text, 'apikey' => $apikey,
            ]);
            $this->line('HTTP '.$res->status());
            $this->line(trim(strip_tags($res->body())));
            return $res->successful() ? self::SUCCESS : self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Error: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
