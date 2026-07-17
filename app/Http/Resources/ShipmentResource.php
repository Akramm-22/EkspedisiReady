<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tracking_number' => $this->tracking_number,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),

            'sender' => $this->whenLoaded('sender', fn () => $this->sender->only(['id', 'name', 'phone'])),
            'receiver' => $this->whenLoaded('receiver', fn () => $this->receiver->only(['id', 'name', 'phone', 'address', 'city'])),

            'origin_branch' => $this->whenLoaded('originBranch', fn () => $this->originBranch->only(['id', 'name', 'city'])),
            'destination_branch' => $this->whenLoaded('destinationBranch', fn () => $this->destinationBranch->only(['id', 'name', 'city'])),
            'courier' => $this->whenLoaded('courier', fn () => $this->courier?->only(['id', 'name'])),
            'rate' => $this->whenLoaded('rate', fn () => $this->rate->only(['id', 'origin_city', 'destination_city', 'price_per_kg', 'estimated_days'])),

            'total_weight' => (float) $this->total_weight,
            'total_price' => (float) $this->total_price,
            'photo_url' => $this->photo ? Storage::disk('public')->url($this->photo) : null,

            'items' => $this->whenLoaded('items'),
            'trackings' => $this->whenLoaded('trackings'),

            'shipment_date' => $this->shipment_date?->toDateString(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
