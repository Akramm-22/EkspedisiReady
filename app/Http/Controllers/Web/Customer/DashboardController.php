<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /** GET /customer/dashboard — daftar shipment yang dikirim customer ini + status bayar. */
    public function index(Request $request): Response
    {
        $customer = $request->user('customer');

        $paginated = $customer
            ->sentShipments()
            ->with(['receiver:id,name,city', 'originBranch:id,name,city', 'destinationBranch:id,name,city', 'payments'])
            ->latest()
            ->paginate(10);

        $shipments = collect($paginated->items())->map(function ($shipment) {
            $latestPayment = $shipment->payments->last();

            return [
                'id' => $shipment->id,
                'tracking_number' => $shipment->tracking_number,
                'status' => $shipment->status,
                'origin_branch' => $shipment->originBranch,
                'destination_branch' => $shipment->destinationBranch,
                'receiver' => $shipment->receiver,
                'total_price' => $shipment->total_price,
                'is_paid' => $latestPayment?->payment_status === 'paid',
            ];
        })->values();

        return Inertia::render('Customer/Dashboard', [
            'shipments' => $shipments,
        ]);

    }
}

