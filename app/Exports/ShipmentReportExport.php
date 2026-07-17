<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShipmentReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Collection $shipments)
    {
    }

    public function collection(): Collection
    {
        return $this->shipments;
    }

    public function headings(): array
    {
        return ['No. Resi', 'Tanggal', 'Pengirim', 'Cabang Asal', 'Tujuan', 'Berat (kg)', 'Total Harga', 'Status'];
    }

    public function map($shipment): array
    {
        return [
            $shipment->tracking_number,
            $shipment->created_at->format('d/m/Y H:i'),
            $shipment->sender->name ?? '-',
            $shipment->originBranch->name ?? '-',
            $shipment->destinationBranch->name ?? '-',
            (float) $shipment->total_weight,
            $shipment->total_price,
            $shipment->status->label(),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
