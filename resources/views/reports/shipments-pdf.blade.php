<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        p.period { color: #64748b; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        th { background: #f0fdf6; }
        tfoot td { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan Pengiriman &mdash; drgEkspedisi</h1>
    <p class="period">Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No. Resi</th>
                <th>Tanggal</th>
                <th>Pengirim</th>
                <th>Cabang Asal</th>
                <th>Cabang Tujuan</th>
                <th>Berat</th>
                <th>Total Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shipments as $shipment)
                <tr>
                    <td>{{ $shipment->tracking_number }}</td>
                    <td>{{ $shipment->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $shipment->sender->name ?? '-' }}</td>
                    <td>{{ $shipment->originBranch->name ?? '-' }}</td>
                    <td>{{ $shipment->destinationBranch->name ?? '-' }}</td>
                    <td>{{ $shipment->total_weight }} kg</td>
                    <td>Rp{{ number_format($shipment->total_price, 0, ',', '.') }}</td>
                    <td>{{ $shipment->status->label() }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">Total</td>
                <td colspan="2">Rp{{ number_format($shipments->sum('total_price'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
