<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Copia los archivos ya existentes en los discos locales hacia Cloudflare R2,
 * conservando las mismas rutas (para que las columnas *_path sigan siendo válidas).
 *
 *   php artisan storage:sync-r2                (públicos + privados)
 *   php artisan storage:sync-r2 --public-only  (sólo previews/miniaturas/QR)
 *   php artisan storage:sync-r2 --private-only (sólo originales/comprobantes)
 *   php artisan storage:sync-r2 --overwrite    (re-sube aunque ya exista en R2)
 */
class SyncStorageToR2 extends Command
{
    protected $signature = 'storage:sync-r2 {--public-only} {--private-only} {--overwrite}';

    protected $description = 'Copia los archivos locales (public/local) a Cloudflare R2 (r2public/r2) conservando las rutas';

    public function handle(): int
    {
        $overwrite = (bool) $this->option('overwrite');
        $doPublic  = ! $this->option('private-only');
        $doPrivate = ! $this->option('public-only');

        $total = 0;

        if ($doPublic) {
            $total += $this->copyDisk('public', 'r2public', $overwrite);
        }
        if ($doPrivate) {
            $total += $this->copyDisk('local', 'r2', $overwrite);
        }

        $this->newLine();
        $this->info("Sincronización terminada. Archivos copiados: {$total}.");
        return self::SUCCESS;
    }

    private function copyDisk(string $from, string $to, bool $overwrite): int
    {
        $src = Storage::disk($from);
        $dst = Storage::disk($to);

        $files = $src->allFiles();
        $n = count($files);
        $this->info("Copiando disco '{$from}' → '{$to}' ({$n} archivos)...");

        if ($n === 0) {
            return 0;
        }

        $bar = $this->output->createProgressBar($n);
        $bar->start();

        $copied = 0; $skipped = 0; $failed = 0;
        foreach ($files as $path) {
            try {
                if (! $overwrite && $dst->exists($path)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }
                $stream = $src->readStream($path);
                $dst->writeStream($path, $stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
                $copied++;
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->warn("  ! Error con {$path}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->line("  '{$from}': copiados {$copied}, ya existían {$skipped}" . ($failed ? ", con error {$failed}" : ''));

        return $copied;
    }
}
