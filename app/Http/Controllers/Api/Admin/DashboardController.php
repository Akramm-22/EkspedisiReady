<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $shipmentQuery = Shipment::query();

        $data = [
            'shipments_today' => (clone $shipmentQuery)->whereDate('created_at', today())->count(),
            'shipments_this_month' => (clone $shipmentQuery)->whereMonth('created_at', now()->month)->count(),
            'delivered_this_month' => (clone $shipmentQuery)->where('status', 'delivered')->whereMonth('updated_at', now()->month)->count(),

            'revenue_this_month' => Payment::where('payment_status', 'paid')->whereMonth('payment_date', now()->month)->sum('amount'),
            'pending_payments' => Payment::where('payment_status', 'pending')->count(),

            'active_couriers' => User::role('courier')->count(),

            'status_breakdown' => (clone $shipmentQuery)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status'),

            'branch_performance' => Branch::withCount(['originShipments as shipments_count' => fn ($q) => $q->whereMonth('created_at', now()->month)])
                ->orderByDesc('shipments_count')->limit(5)->get(['id', 'name', 'city']),
        ];

        return response()->json(['data' => $data]);
    }
}
