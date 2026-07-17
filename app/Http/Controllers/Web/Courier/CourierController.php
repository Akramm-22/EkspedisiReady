<?php

namespace App\Http\Controllers\Web\Courier;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourierController extends Controller
{
    public function __construct(private ShipmentService $shipmentService)
    {
    }

    /** GET /courier — daftar shipment yang ditugaskan ke kurir yang login. */
    public function index(Request $request): View
    {
        $shipments = Shipment::where('courier_id', $request->user()->id)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->latest()
            ->get();

        return view('courier.index', compact('shipments'));
    }

    /** GET /courier/shipments/{shipment} — detail + form update status kurir. */
    public function show(Shipment $shipment): View
    {
        abort_unless($shipment->courier_id === auth()->id(), 403);

        return view('courier.show', [
            'shipment' => $shipment->load('trackings', 'receiver'),
            'nextStatus' => $shipment->status->next(),
        ]);
    }

    /** PATCH /courier/shipments/{shipment}/status — update status dari HP kurir. Hanya boleh maju satu langkah sesuai urutan alur. */
    public function updateStatus(Request $request, Shipment $shipment): RedirectResponse
    {
        abort_unless($shipment->courier_id === auth()->id(), 403);

        $nextStatus = $shipment->status->next();

        $validated = $request->validate([
            'status' => ['required', Rule::in($nextStatus ? [$nextStatus->value] : [])],
            'description' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ], [
            'status.in' => $nextStatus
                ? "Status harus diperbarui berurutan. Status berikutnya yang valid: {$nextStatus->label()}."
                : 'Shipment ini sudah pada status akhir dan tidak bisa diperbarui lagi.',
        ]);

        $this->shipmentService->updateStatus($shipment, $validated);

        if ($request->hasFile('photo')) {
            $shipment->update(['photo' => Storage::disk('public')->putFile('shipments', $request->file('photo'))]);
        }

        return redirect()->route('courier.index')->with('success', 'Status berhasil diperbarui.');
    }
}
