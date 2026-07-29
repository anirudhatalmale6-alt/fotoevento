<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventPackage;
use App\Models\Photo;
use App\Services\WatermarkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount('photos')->latest()->get();
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:160'],
            'event_date'     => ['nullable', 'date'],
            'pin'            => ['nullable', 'string', 'max:12'],
            'currency'       => ['required', 'string', 'max:8'],
            'price_unit'     => ['required', 'numeric', 'min:0'],
            'watermark_text' => ['nullable', 'string', 'max:60'],
        ]);

        $data['slug'] = Event::makeUniqueSlug($data['name']);
        if (empty($data['watermark_text'])) {
            $data['watermark_text'] = config('app.default_watermark', 'MUESTRA');
        }

        $event = Event::create($data);

        // paquetes opcionales (qty[] / price[] / label[])
        $this->syncPackages($request, $event);

        return redirect()->route('admin.events.show', $event)
            ->with('ok', 'Evento creado. Ahora puedes subir las fotos.');
    }

    public function show(Event $event)
    {
        $event->load(['photos', 'packages']);
        return view('admin.events.show', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:160'],
            'event_date'     => ['nullable', 'date'],
            'pin'            => ['nullable', 'string', 'max:12'],
            'currency'       => ['required', 'string', 'max:8'],
            'price_unit'     => ['required', 'numeric', 'min:0'],
            'watermark_text' => ['nullable', 'string', 'max:60'],
        ]);
        $event->update($data);
        $this->syncPackages($request, $event);

        return back()->with('ok', 'Datos del evento actualizados.');
    }

    public function destroy(Event $event)
    {
        Storage::disk(config('storage.public_disk'))->deleteDirectory('events/' . $event->id);
        Storage::disk(config('storage.private_disk'))->deleteDirectory('events/' . $event->id);
        $event->delete();
        return redirect()->route('admin.events.index')->with('ok', 'Evento eliminado.');
    }

    /**
     * Subida masiva. Recibe uno o varios archivos (el frontend los envía por lotes
     * para poder subir miles de fotos con barra de progreso). Devuelve JSON.
     */
    public function uploadPhotos(Request $request, Event $event, WatermarkService $wm)
    {
        $request->validate([
            'photos'   => ['required', 'array'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png', 'max:25600'], // 25MB/foto
        ]);

        $public  = Storage::disk(config('storage.public_disk'));   // previews + miniaturas (visibles)
        $private = Storage::disk(config('storage.private_disk')); // originales SIN marca (nunca accesibles por web)
        $dirOrig    = "events/{$event->id}/orig";
        $dirPreview = "events/{$event->id}/preview";
        $dirThumb   = "events/{$event->id}/thumb";
        $saved = [];
        $sort = (int) $event->photos()->max('sort');

        foreach ($request->file('photos') as $file) {
            $binary = file_get_contents($file->getRealPath());
            $code   = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $base   = uniqid('p_', true);

            $origPath    = "{$dirOrig}/{$base}.jpg";
            $previewPath = "{$dirPreview}/{$base}.jpg";
            $thumbPath   = "{$dirThumb}/{$base}.jpg";

            // El original se guarda en disco privado; sólo se entrega tras el pago (Hito 4, con enlaces firmados).
            $private->put($origPath, $binary);
            $public->put($previewPath, $wm->preview($binary, $event->watermark_text));
            $public->put($thumbPath, $wm->thumb($binary, $event->watermark_text));

            $photo = $event->photos()->create([
                'code'          => $code,
                'original_path' => $origPath,
                'preview_path'  => $previewPath,
                'thumb_path'    => $thumbPath,
                'bytes'         => strlen($binary),
                'sort'          => ++$sort,
            ]);
            $saved[] = ['id' => $photo->id, 'thumb' => $photo->thumbUrl(), 'code' => $photo->code];
        }

        // portada + contador
        $event->update([
            'photos_count' => $event->photos()->count(),
            'cover_thumb'  => $event->cover_thumb ?: ($saved[0]['thumb'] ?? null),
        ]);

        return response()->json(['ok' => true, 'saved' => $saved, 'total' => $event->photos()->count()]);
    }

    /** Elige la foto de portada (la que se muestra al compartir el enlace). */
    public function setCover(Event $event, Photo $photo)
    {
        abort_unless($photo->event_id === $event->id, 404);
        $event->update(['cover_photo_id' => $photo->id]);
        return response()->json(['ok' => true, 'cover_id' => $photo->id]);
    }

    public function destroyPhoto(Event $event, Photo $photo)
    {
        abort_unless($photo->event_id === $event->id, 404);
        Storage::disk(config('storage.private_disk'))->delete($photo->original_path);
        Storage::disk(config('storage.public_disk'))->delete([$photo->preview_path, $photo->thumb_path]);
        $photo->delete();
        $event->update(['photos_count' => $event->photos()->count()]);

        return response()->json(['ok' => true, 'total' => $event->photos()->count()]);
    }

    private function syncPackages(Request $request, Event $event): void
    {
        $qtys   = (array) $request->input('pkg_qty', []);
        $prices = (array) $request->input('pkg_price', []);
        $labels = (array) $request->input('pkg_label', []);

        $event->packages()->delete();
        foreach ($qtys as $i => $qty) {
            $qty   = (int) $qty;
            $price = (float) ($prices[$i] ?? 0);
            if ($qty > 0 && $price > 0) {
                $event->packages()->create([
                    'qty'   => $qty,
                    'price' => $price,
                    'label' => $labels[$i] ?? null,
                ]);
            }
        }
    }
}
