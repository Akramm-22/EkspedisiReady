<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Requests\Shipment\UpdateShipmentStatusRequest;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Rate;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function __construct(private ShipmentService $shipmentService)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Shipment::class);

        $shipments = Shipment::query()
            ->with(['sender:id,name,phone', 'receiver:id,name,city', 'originBranch:id,name,city'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('tracking_number', 'like', "%{$request->search}%"))
            ->when($request->user()->isCourier(), fn ($q) => $q->where('courier_id', $request->user()->id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.shipments.index', ['shipments' => $shipments, 'filters' => $request->only(['status', 'search'])]);
    }

    public function create(): View
    {
        $this->authorize('create', Shipment::class);

        return view('admin.shipments.create', [
            'branches' => Branch::all(['id', 'name', 'city']),
            'rates' => Rate::all(['id', 'origin_city', 'destination_city', 'price_per_kg', 'estimated_days']),
        ]);
    }

    public function store(StoreShipmentRequest $request): RedirectResponse
    {
        $shipment = $this->shipmentService->create($request->safe()->except(['photo']), $request->file('photo'));

        return redirect()->route('admin.shipments.show', $shipment)
            ->with('success', "Shipment {$shipment->tracking_number} berhasil dibuat.");
    }

    public function show(Shipment $shipment): View
    {
        $this->authorize('view', $shipment);

        $shipment->load(['sender', 'receiver', 'originBranch', 'destinationBranch', 'rate', 'courier', 'items', 'trackings']);

        return view('admin.shipments.show', compact('shipment'));
    }

    public function updateStatus(UpdateShipmentStatusRequest $request, Shipment $shipment): RedirectResponse
    {
        $this->authorize('update', $shipment);

        $this->shipmentService->updateStatus($shipment, $request->validated());

        return back()->with('success', 'Status pengiriman diperbarui.');
    }

    public function assignCourier(Request $request, Shipment $shipment): RedirectResponse
    {
        $this->authorize('assignCourier', $shipment);

        $request->validate(['courier_id' => ['required', 'exists:users,id']]);

        $shipment->update(['courier_id' => $request->courier_id]);

        return back()->with('success', 'Kurir berhasil ditugaskan.');
    }

    /** Autocomplete customer (sender/receiver) dipanggil via Alpine fetch() di form create. */
    public function searchCustomers(Request $request)
    {
        return Customer::where('name', 'like', "%{$request->q}%")
            ->orWhere('phone', 'like', "%{$request->q}%")
            ->limit(10)
            ->get(['id', 'name', 'phone', 'address', 'city']);
    }
}
