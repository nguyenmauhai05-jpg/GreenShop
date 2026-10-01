<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiaoDichThanhToan extends Model
{
    protected $table = 'giao_dich_thanh_toan';

    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'payment_id',
        'provider',
        'merchant_transaction_id',
        'provider_request_id',
        'provider_transaction_id',
        'amount',
        'status',
        'response_code',
        'response_message',
        'payment_url',
        'expires_at',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function thanhToan(): BelongsTo
    {
        return $this->belongsTo(ThanhToan::class, 'payment_id', 'payment_id');
    }
}
