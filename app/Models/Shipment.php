<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Shipment extends Model
{
    protected $fillable = [
        'tracking_number', 'sender_id', 'receiver_id', 'origin_branch_id',
        'destination_branch_id', 'courier_id', 'rate_id', 'total_weight',
        'total_price', 'status', 'shipment_date', 'photo',
    ];

    protected $casts = [
        'status' => ShipmentStatus::class,
        'total_weight' => 'decimal:2',
        'total_price' => 'decimal:2',
        'shipment_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Shipment $shipment) {
            if (empty($shipment->tracking_number)) {
                $shipment->tracking_number = static::generateTrackingNumber();
            }
        });
    }

    /** Format: DRG-YYYYMMDD-XXXX, dikunci transaksi supaya tidak duplikat. */
    public static function generateTrackingNumber(): string
    {
        return DB::transaction(function () {
            $prefix = 'DRG-'.now()->format('Ymd').'-';

            $count = static::where('tracking_number', 'like', "{$prefix}%")->lockForUpdate()->count();

            return $prefix.str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
        });
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'receiver_id');
    }

    public function originBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'origin_branch_id');
    }

    public function destinationBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(ShipmentTracking::class)->orderBy('tracked_at');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
