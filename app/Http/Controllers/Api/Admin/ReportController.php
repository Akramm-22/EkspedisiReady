<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\ShipmentReportExport;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function operational(Request $request)
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $query = Shipment::whereBetween('created_at', [$validated['from'], $validated['to']]);

        return response()->json(['data' => [
            'total_shipments' => (clone $query)->count(),
            'by_status' => (clone $query)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status'),
            'by_branch' => (clone $query)->join('branches', 'branches.id', '=', 'shipments.origin_branch_id')
                ->select('branches.name', DB::raw('count(*) as total'))->groupBy('branches.name')->pluck('total', 'name'),
        ]]);
    }

    public function financial(Request $request)
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $paid = Payment::where('payment_status', 'paid')->whereBetween('payment_date', [$validated['from'], $validated['to']]);

        return response()->json(['data' => [
            'total_revenue' => (clone $paid)->sum('amount'),
            'total_transactions' => (clone $paid)->count(),
            'by_method' => (clone $paid)->select('payment_method', DB::raw('sum(amount) as total'))->groupBy('payment_method')->pluck('total', 'payment_method'),
            'pending_amount' => Payment::where('payment_status', 'pending')->sum('amount'),
        ]]);
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
