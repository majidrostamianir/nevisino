<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigipayTransaction extends Model
{
    protected $fillable = [
        // لینک‌ها
        'order_id',
        'transaction_id',

        // اطلاعات پایه
        'provider_id',
        'tracking_code',
        'ticket',

        // اطلاعات کاربر
        'cell_number',
        'amount',

        // نوع و درگاه
        'type',
        'preferred_gateway',

        // وضعیت
        'status',

        // callback
        'callback_url',
        'redirect_url',

        // اطلاعات وریفای
        'ref_id',
        'rrn',
        'masked_pan',
        'psp_code',
        'psp_name',
        'terminal_id',

        // payload‌ها
        'request_payload',
        'response_payload',
        'verify_payload',

        // زمان‌ها
        'verified_at',
        'delivered_at',
        'refunded_at',
    ];

    protected $casts = [
        'request_payload'  => 'array',
        'response_payload' => 'array',
        'verify_payload'   => 'array',
        'verified_at'      => 'datetime',
        'delivered_at'     => 'datetime',
        'refunded_at'      => 'datetime',
        'amount'           => 'integer',
        'type'             => 'integer',
        'preferred_gateway'=> 'integer',
    ];

    // ─────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // ─────────────────────────────────────────────
    // Status Constants
    // ─────────────────────────────────────────────

    const STATUS_PENDING   = 'pending';
    const STATUS_VERIFIED  = 'verified';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_REFUNDED  = 'refunded';
    const STATUS_REVERSED  = 'reversed';
    const STATUS_FAILED    = 'failed';

    // ─────────────────────────────────────────────
    // Type Constants (Ticket Types)
    // ─────────────────────────────────────────────

    const TYPE_IPG         = 0;
    const TYPE_WALLET      = 11;
    const TYPE_CREDIT      = 5;
    const TYPE_BNPL        = 13;
    const TYPE_CREDIT_CARD = 24;

    // ─────────────────────────────────────────────
    // Preferred Gateway Constants
    // ─────────────────────────────────────────────

    const GATEWAY_WALLET = 0;
    const GATEWAY_IPG    = 2;

    // ─────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    // ─────────────────────────────────────────────
    // Helper Methods
    // ─────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    public function isReversed(): bool
    {
        return $this->status === self::STATUS_REVERSED;
    }

    /**
     * آیا این تراکنش قابل بازگشت وجه دستی (reverse) است؟
     * فقط تا ۲۵ دقیقه بعد از verify
     */
    public function canBeReversed(): bool
    {
        if (!$this->isVerified() || !$this->verified_at) {
            return false;
        }

        return $this->verified_at->diffInMinutes(now()) < 25;
    }

    /**
     * مارک کردن به عنوان verify شده
     */
    public function markAsVerified(array $verifyResult = []): void
    {
        $this->update([
            'status'         => self::STATUS_VERIFIED,
            'verified_at'    => now(),
            'verify_payload' => $verifyResult,
            'ref_id'         => $verifyResult['rrn'] ?? $this->ref_id,
            'rrn'            => $verifyResult['rrn'] ?? $this->rrn,
            'masked_pan'     => $verifyResult['maskedPan'] ?? $this->masked_pan,
            'psp_code'       => $verifyResult['pspCode'] ?? $this->psp_code,
            'psp_name'       => $verifyResult['pspName'] ?? $this->psp_name,
            'terminal_id'    => $verifyResult['terminalId'] ?? $this->terminal_id,
        ]);
    }

    /**
     * مارک کردن به عنوان ناموفق
     */
    public function markAsFailed(array $payload = []): void
    {
        $this->update([
            'status'           => self::STATUS_FAILED,
            'response_payload' => array_merge($this->response_payload ?? [], $payload),
        ]);
    }
}