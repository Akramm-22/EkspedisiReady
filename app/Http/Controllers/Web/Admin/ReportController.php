<?php

namespace App\Http\Controllers\Web\Admin;

use App\Exports\ShipmentReportExport;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $shipmentQuery = Shipment::whereBetween('created_at', [$from, $to]);
        $paidQuery = Payment::where('payment_status', 'paid')->whereBetween('payment_date', [$from, $to]);

        $operational = [
            'total_shipments' => (clone $shipmentQuery)->count(),
            'by_status' => (clone $shipmentQuery)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status'),
        ];

        $financial = [
            'total_revenue' => (clone $paidQuery)->sum('amount'),
            'total_transactions' => (clone $paidQuery)->count(),
            'by_method' => (clone $paidQuery)->select('payment_method', DB::raw('sum(amount) as total'))->groupBy('payment_method')->pluck('total', 'payment_method'),
        ];

        return view('admin.reports.index', compact('operational', 'financial', 'from', 'to'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'format' => ['required', 'in:xlsx,pdf'],
        ]);

        $shipments = Shipment::with(['sender', 'originBranch', 'destinationBranch'])
            ->whereBetween('created_at', [$validated['from'], $validated['to']])
            ->get();

        $filename = "laporan-pengiriman-{$validated['from']}-sd-{$validated['to']}";

        if ($validated['format'] === 'xlsx') {
            return Excel::download(new ShipmentReportExport($shipments), "{$filename}.xlsx");
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.shipments-pdf', [
            'shipments' => $shipments, 'from' => $validated['from'], 'to' => $validated['to'],
        ]);

        return $pdf->download("{$filename}.pdf");
    }
}
