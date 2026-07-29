<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    public $timestamps = false;

    protected $fillable = ['event_id', 'photo_id', 'type', 'visitor', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public const VIEW    = 'gallery_view';
    public const PREVIEW = 'photo_preview';

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    /** Registra un evento de analítica (una sola línea, sin datos personales). */
    public static function record(string $type, int $eventId, ?int $photoId, string $visitor): void
    {
        static::create([
            'event_id'   => $eventId,
            'photo_id'   => $photoId,
            'type'       => $type,
            'visitor'    => $visitor,
            'created_at' => now(),
        ]);
    }
}
