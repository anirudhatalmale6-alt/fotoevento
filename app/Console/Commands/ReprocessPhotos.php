<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Photo;
use App\Services\WatermarkService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Regenera las vistas previas y miniaturas a partir de los ORIGINALES ya subidos.
 * Útil tras mejorar la marca de agua o corregir la orientación EXIF: no hace falta
 * volver a subir las fotos, se reprocesan las que ya están en el servidor.
 *
 *   php artisan photos:reprocess           (todas)
 *   php artisan photos:reprocess --event=3 (sólo un evento)
 */
class ReprocessPhotos extends Command
{
    protected $signature = 'photos:reprocess {--event= : ID del evento a reprocesar}';

    protected $description = 'Regenera previews/miniaturas desde los originales (aplica orientación EXIF y marca de agua)';

    public function handle(WatermarkService $wm): int
    {
        $private = Storage::disk('local');
        $public  = Storage::disk('public');

        $query = Photo::query()->with('event');
        if ($eventId = $this->option('event')) {
            $query->where('event_id', $eventId);
        }

        $total = $query->count();
        if ($total === 0) {
            $this->warn('No hay fotos para reprocesar.');
            return self::SUCCESS;
        }

        $this->info("Reprocesando {$total} foto(s)...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $ok = 0; $fail = 0;
        $query->chunkById(50, function ($photos) use ($wm, $private, $public, $bar, &$ok, &$fail) {
            foreach ($photos as $photo) {
                try {
                    if (! $private->exists($photo->original_path)) {
                        $fail++; $bar->advance(); continue;
                    }
                    $binary = $private->get($photo->original_path);
                    $text = $photo->event?->watermark_text ?: config('app.default_watermark', 'MUESTRA');

                    $public->put($photo->preview_path, $wm->preview($binary, $text));
                    $public->put($photo->thumb_path, $wm->thumb($binary, $text));
                    $ok++;
                } catch (\Throwable $e) {
                    $fail++;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("Listo. Regeneradas: {$ok}" . ($fail ? "  ·  con error: {$fail}" : ''));

        return self::SUCCESS;
    }
}
