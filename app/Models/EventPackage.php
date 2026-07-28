<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPackage extends Model
{
    protected $fillable = ['event_id', 'label', 'qty', 'price'];

    protected $casts = ['price' => 'decimal:2'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
