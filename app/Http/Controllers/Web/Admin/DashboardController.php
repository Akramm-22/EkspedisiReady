<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $summary = [
            'shipments_today' => Shipment::whereDate('created_at', today())->count(),
            'shipments_this_month' => Shipment::whereMonth('created_at', now()->month)->count(),
            'delivered_this_month' => Shipment::where('status', 'delivered')->whereMonth('updated_at', now()->month)->count(),
            'revenue_this_month' => Payment::where('payment_status', 'paid')->whereMonth('payment_date', now()->month)->sum('amount'),
            'active_couriers' => User::role('courier')->count(),
            'status_breakdown' => Shipment::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status'),
        ];

        $recentShipments = Shipment::with(['sender:id,name'])->latest()->limit(8)->get();

        $branchPerformance = Branch::withCount(['originShipments as shipments_count' => fn ($q) => $q->whereMonth('created_at', now()->month)])
            ->orderByDesc('shipments_count')->limit(5)->get();

        return view('admin.dashboard.index', compact('summary', 'recentShipments', 'branchPerformance'));
    }
}
