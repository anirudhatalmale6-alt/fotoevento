<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StorageController extends Controller
{
    private const FREE_BYTES = 10 * 1024 * 1024 * 1024; // 10 GB gratis en R2

    public function index(Request $request)
    {
        if ($request->boolean('fresh')) {
            Cache::forget('storage_usage');
        }

        $data = Cache::remember('storage_usage', 300, fn () => $this->compute());

        $data['free_bytes'] = self::FREE_BYTES;
        $data['pct'] = self::FREE_BYTES > 0
            ? min(100, round($data['used'] / self::FREE_BYTES * 100, 1)) : 0;
        $data['remaining'] = max(0, self::FREE_BYTES - $data['used']);

        // Estimación de cuántas fotos más caben (según el peso promedio por foto).
        $perPhoto = $data['orig_count'] > 0 ? $data['used'] / $data['orig_count'] : 0;
        $data['photos_left'] = $perPhoto > 0 ? (int) floor($data['remaining'] / $perPhoto) : null;

        return view('admin.storage.index', $data);
    }

    private function compute(): array
    {
        try {
            $client = new S3Client([
                'region'   => 'auto',
                'version'  => 'latest',
                'endpoint' => config('filesystems.disks.r2.endpoint'),
                'use_path_style_endpoint' => true,
                'credentials' => [
                    'key'    => config('filesystems.disks.r2.key'),
                    'secret' => config('filesystems.disks.r2.secret'),
                ],
            ]);

            $priv = $this->scan($client, config('filesystems.disks.r2.bucket'));
            $pub  = $this->scan($client, config('filesystems.disks.r2public.bucket'));
        } catch (\Throwable $e) {
            // Si R2 no está disponible, no rompas el panel.
            return [
                'used' => 0, 'orig_count' => 0, 'error' => true,
                'cats' => [], 'events' => [],
            ];
        }

        $cats = [
            'orig'     => $priv['cats']['orig']     + $pub['cats']['orig'],
            'preview'  => $priv['cats']['preview']  + $pub['cats']['preview'],
            'thumb'    => $priv['cats']['thumb']    + $pub['cats']['thumb'],
            'receipts' => $priv['cats']['receipts'] + $pub['cats']['receipts'],
            'other'    => $priv['cats']['other']    + $pub['cats']['other'],
        ];
        $used = array_sum($cats);

        // Uso por evento (suma de ambos buckets).
        $perEvent = $priv['perEvent'];
        foreach ($pub['perEvent'] as $id => $b) {
            $perEvent[$id] = ($perEvent[$id] ?? 0) + $b;
        }
        arsort($perEvent);
        $names = Event::pluck('name', 'id');
        $events = [];
        foreach ($perEvent as $id => $bytes) {
            $events[] = ['name' => $names[$id] ?? ('Evento #'.$id), 'bytes' => $bytes];
        }

        return [
            'used'       => $used,
            'orig_count' => $priv['origCount'] + $pub['origCount'],
            'cats'       => $cats,
            'events'     => $events,
            'error'      => false,
        ];
    }

    private function scan(S3Client $client, ?string $bucket): array
    {
        $cats = ['orig' => 0, 'preview' => 0, 'thumb' => 0, 'receipts' => 0, 'other' => 0];
        $perEvent = [];
        $origCount = 0;
        $token = null;

        do {
            $params = ['Bucket' => $bucket, 'MaxKeys' => 1000];
            if ($token) {
                $params['ContinuationToken'] = $token;
            }
            $res = $client->listObjectsV2($params);
            foreach (($res['Contents'] ?? []) as $obj) {
                $key = $obj['Key'];
                $size = (int) $obj['Size'];

                if (str_contains($key, '/orig/')) { $cats['orig'] += $size; $origCount++; }
                elseif (str_contains($key, '/preview/')) { $cats['preview'] += $size; }
                elseif (str_contains($key, '/thumb/')) { $cats['thumb'] += $size; }
                elseif (str_starts_with($key, 'receipts/')) { $cats['receipts'] += $size; }
                else { $cats['other'] += $size; }

                if (preg_match('#events/(\d+)/#', $key, $m)) {
                    $id = (int) $m[1];
                    $perEvent[$id] = ($perEvent[$id] ?? 0) + $size;
                }
            }
            $token = ($res['IsTruncated'] ?? false) ? $res['NextContinuationToken'] : null;
        } while ($token);

        return compact('cats', 'perEvent', 'origCount');
    }
}
