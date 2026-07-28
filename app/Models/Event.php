<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $fillable = [
        'name', 'slug', 'event_date', 'pin', 'currency',
        'price_unit', 'watermark_text', 'cover_thumb', 'published',
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
