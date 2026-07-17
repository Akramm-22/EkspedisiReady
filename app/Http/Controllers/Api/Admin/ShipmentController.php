<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\AssignCourierRequest;
use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Requests\Shipment\UpdateShipmentStatusRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(private ShipmentService $shipmentService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Shipment::class);

        $shipments = Shipment::query()
            ->with(['sender:id,name,phone', 'originBranch:id,name,city'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('tracking_number', 'like', "%{$request->search}%"))
            ->when($request->user()->isCourier(), fn ($q) => $q->where('courier_id', $request->user()->id))
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'data' => ShipmentResource::collection($shipments),
            'meta' => [
                'current_page' => $shipments->currentPage(),
                'last_page' => $shipments->lastPage(),
                'total' => $shipments->total(),
            ],
        ]);
    }

    public function store(StoreShipmentRequest $request): JsonResponse
    {
        $shipment = $this->shipmentService->create($request->safe()->except(['photo']), $request->file('photo'));

        return response()->json([
            'message' => 'Shipment berhasil dibuat.',
            'data' => new ShipmentResource($shipment->load(['items', 'trackings'])),
        ], 201);
    }

    public function show(Shipment $shipment): JsonResponse
    {
        $this->authorize('view', $shipment);

        $shipment->load(['sender', 'receiver', 'originBranch', 'destinationBranch', 'courier', 'rate', 'items', 'trackings']);

        return response()->json(['data' => new ShipmentResource($shipment)]);
    }

    public function updateStatus(UpdateShipmentStatusRequest $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('update', $shipment);

        $this->shipmentService->updateStatus($shipment, $request->validated());

        return response()->json([
            'message' => 'Status pengiriman diperbarui.',
            'data' => new ShipmentResource($shipment->fresh(['trackings'])),
        ]);
    }

    public function assignCourier(AssignCourierRequest $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('assignCourier', $shipment);

        $shipment->update(['courier_id' => $request->validated('courier_id')]);

        return response()->json([
            'message' => 'Kurir berhasil ditugaskan.',
            'data' => new ShipmentResource($shipment->load('courier')),
        ]);
    }

    public function destroy(Shipment $shipment): JsonResponse
    {
        $this->authorize('delete', $shipment);

        if ($shipment->status !== ShipmentStatus::Pending) {
            return response()->json(['message' => 'Shipment yang sudah diproses tidak dapat dihapus.'], 422);
        }

        $shipment->delete();

        return response()->json(['message' => 'Shipment berhasil dihapus.']);
    }
}
