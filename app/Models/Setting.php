<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting.$key", function () use ($key, $default) {
            return static::query()->find($key)->value ?? $default;
        });
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.$key");
    }

    /** Datos de Yape para las pantallas de pago. */
    public static function yape(): array
    {
        return [
            'number'  => static::get('yape_number'),
            'account' => static::get('yape_account'),
            'qr_path' => static::get('yape_qr_path'),
        ];
    }
}
