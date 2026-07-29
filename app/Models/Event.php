<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $fillable = [
        'name', 'slug', 'event_date', 'pin', 'currency',
        'price_unit', 'watermark_text', 'cover_thumb', 'cover_photo_id', 'published',
        'photos_count',
    ];

    protected $casts = [
        'event_date' => 'date',
        'published'  => 'boolean',
        'price_unit' => 'decimal:2',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->orderBy('sort')->orderBy('id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(EventPackage::class)->orderBy('qty');
    }

    public function coverPhoto(): BelongsTo
    {
        return $this->belongsTo(Photo::class, 'cover_photo_id');
    }

    /** Foto usada en la vista previa al compartir el enlace (portada elegida o la primera). */
    public function shareImageUrl(): ?string
    {
        $photo = $this->coverPhoto ?: $this->photos()->first();
        return $photo?->previewUrl();
    }

    public static function makeUniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'evento';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    // Enlace privado de la galería
    public function galleryUrl(): string
    {
        return url('/g/' . $this->slug);
    }
}
