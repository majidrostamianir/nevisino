<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BasalamProduct extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',   // ← این
        'basalam_product_id',
        'basalam_sku',
        'status',
        'synced_at',
        'last_error',
        'payload',
        'response',
    ];
    protected $casts = [
        'synced_at' => 'datetime',
        'payload'   => 'array',
        'response'  => 'array',
    ];

    // ---------- Relations ----------

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // ---------- Scopes ----------

    public function scopeSynced($q)
    {
        return $q->where('status', 'synced');
    }

    public function scopeFailed($q)
    {
        return $q->where('status', 'failed');
    }

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    // ---------- Helpers ----------

    public function markSynced(string $basalamId): void
    {
        $this->update([
            'basalam_product_id' => $basalamId,
            'status'             => 'synced',
            'synced_at'          => now(),
            'last_error'         => null,
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status'     => 'failed',
            'last_error' => $error,
        ]);
    }
}