<?php

namespace App\Http\Controllers\Api\Public;

use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TrackingController extends Controller
{
    /** GET /api/track/{tracking_number} — tracking publik: nomor resi -> timeline lengkap. */
    public function show(string $trackingNumber): JsonResponse
    {
        $shipment = Shipment::query()
            ->with([
                'originBranch:id,name,city',
                'destinationBranch:id,name,city',
                'trackings' => fn ($q) => $q->orderBy('tracked_at'),
            ])
            ->where('tracking_number', $trackingNumber)
            ->first();

        if (! $shipment) {
            throw ValidationException::withMessages([
                'tracking_number' => 'Nomor resi tidak ditemukan. Periksa kembali nomor resi Anda.',
            ]);
        }

        return response()->json([
            'data' => [
                'tracking_number' => $shipment->tracking_number,
                'status' => $shipment->status->value,
                'status_label' => $shipment->status->label(),
                'origin' => $shipment->originBranch?->city,
                'destination' => $shipment->destinationBranch?->city,
                'timeline' => $this->buildTimeline($shipment),
            ],
        ]);
    }

    private function buildTimeline(Shipment $shipment): array
    {
        $history = $shipment->trackings->keyBy('status');

        return collect(ShipmentStatus::timelineOrder())->map(function (ShipmentStatus $status) use ($history) {
            $entry = $history->get($status->value);

            return [
                'status' => $status->value,
                'label' => $status->label(),
                'is_completed' => (bool) $entry,
                'location' => $entry?->location,
                'description' => $entry?->description,
                'timestamp' => $entry?->tracked_at?->toIso8601String(),
            ];
        })->values()->all();
    }
}
