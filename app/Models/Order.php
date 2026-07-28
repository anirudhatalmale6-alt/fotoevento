<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'event_id', 'code', 'token', 'customer_name', 'customer_contact', 'customer_email',
        'photo_count', 'subtotal', 'total', 'applied_label', 'status', 'note',
        'receipt_path', 'op_code', 'paid_at', 'approved_at',
    ];

    protected $casts = [
        'subtotal'    => 'decimal:2',
        'total'       => 'decimal:2',
        'paid_at'     => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Genera una referencia única y legible para el cliente: FE-0007 */
    public static function makeCode(): string
    {
        do {
            $code = 'FE-' . str_pad((string) (static::max('id') + 1), 4, '0', STR_PAD_LEFT);
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public static function makeToken(): string
    {
        return Str::random(40);
    }

    /**
     * Estados del pedido:
     *  pendiente   -> cliente aún no envía comprobante
     *  comprobante -> comprobante recibido, esperando aprobación del fotógrafo
     *  aprobado    -> pago confirmado, descarga habilitada
     *  rechazado   -> comprobante no válido, cliente puede reintentar
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'comprobante' => 'Comprobante recibido',
            'aprobado'    => 'Aprobado',
            'rechazado'   => 'Rechazado',
            default       => 'Pendiente de pago',
        };
    }

    public function isApproved(): bool
    {
        return $this->status === 'aprobado';
    }

    public function hasReceipt(): bool
    {
        return filled($this->receipt_path);
    }
}
