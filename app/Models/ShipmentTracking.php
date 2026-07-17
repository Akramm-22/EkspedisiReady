<?php

namespace App\Models;

use App\Events\ShipmentStatusUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentTracking extends Model
{
    protected $fillable = ['shipment_id', 'location', 'description', 'status', 'tracked_at'];

    protected $casts = [
        'tracked_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Setiap ada tracking baru: sinkronkan status induk shipment lalu
        // broadcast event (dipakai tracking publik realtime & GPS kurir).
        static::created(function (ShipmentTracking $tracking) {
            $tracking->shipment()->update(['status' => $tracking->status]);

            event(new ShipmentStatusUpdated($tracking));
        });
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
