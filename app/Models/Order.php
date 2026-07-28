<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'event_id', 'code', 'customer_name', 'customer_contact', 'customer_email',
        'photo_count', 'subtotal', 'total', 'applied_label', 'status', 'note',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Genera una referencia única y legible para el cliente: FE-2025-0007 */
    public static function makeCode(): string
    {
        do {
            $code = 'FE-' . str_pad((string) (static::max('id') + 1), 4, '0', STR_PAD_LEFT);
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pagado'    => 'Pagado',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
            default     => 'Pendiente',
        };
    }
}
