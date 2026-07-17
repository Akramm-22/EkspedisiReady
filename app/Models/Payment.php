<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'shipment_id', 'amount', 'payment_method', 'payment_status', 'payment_date',
        'midtrans_order_id', 'midtrans_transaction_id', 'midtrans_snap_token',
        'midtrans_transaction_status', 'midtrans_raw_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'midtrans_raw_response' => 'array',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /** Cash dibayar langsung ke kasir; transfer & e-wallet lewat Midtrans Snap. */
    public function usesMidtrans(): bool
    {
        return in_array($this->payment_method, ['transfer', 'e-wallet'], true);
    }
}
