<?php

namespace App\Services;

/**
 * Genera, con GD, las versiones derivadas de cada foto:
 *   - preview: vista previa (máx 1000px) CON marca de agua diagonal en mosaico
 *   - thumb:   miniatura (máx 500px) CON marca de agua
 * El original NUNCA lleva marca y no se publica en la galería.
 *
 * La marca de agua es texto (nombre/marca del fotógrafo). En una siguiente etapa
 * se puede sumar marca de agua por imagen (logo PNG) sin cambiar este flujo.
 */
class WatermarkService
{
    private string $fontPath;

    public function __construct()
    {
        $this->fontPath = resource_path('fonts/wm.ttf');
    }

    /** Devuelve binario JPEG de la vista previa con marca de agua. */
    public function preview(string $binary, string $text): string
    {
        return $this->render($binary, $text, 1000, 22, 90, 3, 4);
    }

    /** Devuelve binario JPEG de la miniatura con marca de agua. */
    public function thumb(string $binary, string $text): string
    {
        // menos densidad de texto en la miniatura
        return $this->render($binary, $text, 500, 20, 95, 4, 5);
    }

    private function render(string $binary, string $text, int $max, int $div, int $alpha, int $gapX, int $gapY): string
    {
        $src = @imagecreatefromstring($binary);
        if ($src === false) {
            throw new \RuntimeException('No se pudo leer la imagen.');
        }
        $w = imagesx($src);
        $h = imagesy($src);

        // redimensionar manteniendo proporción
        $scale = min(1.0, $max / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));

        $img = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($img, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($src);

        imagealphablending($img, true);

        $fontSize = max(12, (int) floor($nw / $div));
        $white = imagecolorallocatealpha($img, 255, 255, 255, $alpha); // alpha 0-127 (mayor = más transparente)
        $angle = 28;

        // medir el texto para calcular el paso del mosaico
        $bbox = imagettfbbox($fontSize, $angle, $this->fontPath, $text);
        $tw = abs($bbox[2] - $bbox[0]);
        $th = abs($bbox[7] - $bbox[1]);
        $stepX = max(60, $tw + $fontSize * $gapX);
        $stepY = max(60, $th + $fontSize * $gapY);

        for ($y = -$th; $y < $nh + $stepY; $y += $stepY) {
            for ($x = -$tw; $x < $nw + $stepX; $x += $stepX) {
                imagettftext($img, $fontSize, $angle, (int) $x, (int) $y, $white, $this->fontPath, $text);
            }
        }

        ob_start();
        imagejpeg($img, null, $max > 600 ? 82 : 80);
        $out = ob_get_clean();
        imagedestroy($img);

        return $out;
    }
}
