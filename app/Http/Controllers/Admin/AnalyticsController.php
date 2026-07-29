<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Event;
use App\Models\Order;
use App\Models\Photo;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /** Resumen general + tabla por evento. */
    public function index()
    {
        $events = Event::orderByDesc('id')->get();

        // Métricas de analítica agrupadas por evento.
        $views = AnalyticsEvent::where('type', AnalyticsEvent::VIEW)
            ->select('event_id',
                DB::raw('COUNT(*) as visits'),
                DB::raw('COUNT(DISTINCT visitor) as visitors'))
            ->groupBy('event_id')->get()->keyBy('event_id');

        $previews = AnalyticsEvent::where('type', AnalyticsEvent::PREVIEW)
            ->select('event_id',
                DB::raw('COUNT(*) as previews'),
                DB::raw('COUNT(DISTINCT visitor) as prev_visitors'))
            ->groupBy('event_id')->get()->keyBy('event_id');

        $orders = Order::select('event_id', DB::raw('COUNT(*) as orders'))
            ->groupBy('event_id')->get()->keyBy('event_id');

        $rows = $events->map(function ($e) use ($views, $previews, $orders) {
            $visitors = (int) ($views[$e->id]->visitors ?? 0);
            $ordersN  = (int) ($orders[$e->id]->orders ?? 0);
            return [
                'event'         => $e,
                'visitors'      => $visitors,
                'visits'        => (int) ($views[$e->id]->visits ?? 0),
                'previews'      => (int) ($previews[$e->id]->previews ?? 0),
                'prev_visitors' => (int) ($previews[$e->id]->prev_visitors ?? 0),
                'orders'        => $ordersN,
                'conv'          => $visitors > 0 ? round($ordersN / $visitors * 100, 1) : null,
            ];
        });

        $totals = [
            'visitors' => $rows->sum('visitors'),
            'visits'   => $rows->sum('visits'),
            'previews' => $rows->sum('previews'),
            'orders'   => $rows->sum('orders'),
        ];
        $totals['conv'] = $totals['visitors'] > 0
            ? round($totals['orders'] / $totals['visitors'] * 100, 1) : null;

        return view('admin.analytics.index', compact('rows', 'totals'));
    }

    /** Detalle de un evento: fotos más vistas + actividad por día. */
    public function show(Event $event)
    {
        $visitors = (int) AnalyticsEvent::where('event_id', $event->id)
            ->where('type', AnalyticsEvent::VIEW)->distinct('visitor')->count('visitor');
        $visits = (int) AnalyticsEvent::where('event_id', $event->id)
            ->where('type', AnalyticsEvent::VIEW)->count();
        $previews = (int) AnalyticsEvent::where('event_id', $event->id)
            ->where('type', AnalyticsEvent::PREVIEW)->count();
        $orders = (int) Order::where('event_id', $event->id)->count();

        // Fotos más previsualizadas.
        $topRaw = AnalyticsEvent::where('event_id', $event->id)
            ->where('type', AnalyticsEvent::PREVIEW)
            ->whereNotNull('photo_id')
            ->select('photo_id',
                DB::raw('COUNT(*) as views'),
                DB::raw('COUNT(DISTINCT visitor) as people'))
            ->groupBy('photo_id')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->limit(24)->get();

        $photos = Photo::whereIn('id', $topRaw->pluck('photo_id'))->get()->keyBy('id');
        $topPhotos = $topRaw->map(fn ($r) => [
            'photo'  => $photos[$r->photo_id] ?? null,
            'views'  => (int) $r->views,
            'people' => (int) $r->people,
        ])->filter(fn ($r) => $r['photo']);
        $maxPhotoViews = (int) ($topRaw->max('views') ?: 1);

        // Actividad de los últimos 14 días.
        $since = now()->subDays(13)->startOfDay();
        $daily = AnalyticsEvent::where('event_id', $event->id)
            ->where('created_at', '>=', $since)
            ->select(
                DB::raw('DATE(created_at) as d'),
                DB::raw("SUM(type='gallery_view') as views"),
                DB::raw("SUM(type='photo_preview') as previews"))
            ->groupBy(DB::raw('DATE(created_at)'))->get()->keyBy('d');

        $days = [];
        $maxDay = 1;
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $v = (int) ($daily[$date]->views ?? 0);
            $p = (int) ($daily[$date]->previews ?? 0);
            $maxDay = max($maxDay, $v, $p);
            $days[] = ['date' => $date, 'views' => $v, 'previews' => $p];
        }

        return view('admin.analytics.show', compact(
            'event', 'visitors', 'visits', 'previews', 'orders',
            'topPhotos', 'maxPhotoViews', 'days', 'maxDay'
        ));
    }
}
