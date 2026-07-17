<?php

namespace App\Events;

use App\Enums\ShipmentStatus;
use App\Models\ShipmentTracking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class ShipmentStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public ShipmentTracking $tracking)
    {
    }

    /** Broadcast realtime ke channel publik tracking (dipakai halaman lacak). */
    public function broadcastOn(): array
    {
        return [
            new Channel('shipment.'.$this->tracking->shipment->tracking_number),
        ];
    }

    public function broadcastAs(): string
    {
        return 'status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'tracking_number' => $this->tracking->shipment->tracking_number,
            'status' => $this->tracking->status,
            'label' => ShipmentStatus::from($this->tracking->status)->label(),
            'location' => $this->tracking->location,
            'description' => $this->tracking->description,
            'created_at' => $this->tracking->created_at->toIso8601String(),
        ];
    }
}
