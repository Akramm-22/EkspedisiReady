<?php

namespace App\Services;

use App\Enums\ShipmentStatus;
use App\Models\Rate;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ShipmentService
{
    /**
     * Buat shipment baru + item + tracking awal, dalam satu Database
     * Transaction. Sesuai PDM, foto disimpan sebagai path string langsung
     * di kolom `shipments.photo` / `shipment_items.photo` — tidak ada
     * tabel foto terpisah.
     */
    public function create(array $data, ?UploadedFile $photo = null): Shipment
    {
        return DB::transaction(function () use ($data, $photo) {
            $rate = Rate::findOrFail($data['rate_id']);
            $totalWeight = collect($data['items'])->sum('weight');

            $shipment = Shipment::create([
                ...collect($data)->except(['items', 'photo'])->all(),
                'total_weight' => $totalWeight,
                'total_price' => $rate->calculate($totalWeight),
                'status' => ShipmentStatus::Pending,
                'shipment_date' => now()->toDateString(),
                'photo' => $photo ? Storage::disk('public')->putFile('shipments', $photo) : null,
            ]);

            foreach ($data['items'] as $item) {
                $shipment->items()->create($item);
            }

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'status' => ShipmentStatus::Pending->value,
                'location' => $shipment->originBranch->city ?? null,
                'description' => 'Pesanan diterima, menunggu penjemputan kurir.',
                'tracked_at' => now(),
            ]);

            return $shipment->fresh(['items', 'trackings']);
        });
    }

    /**
     * Update status shipment secara berurutan sesuai alur linear
     * (Pending → PickedUp → InTransit → ArrivedAtBranch → OutForDelivery
     * → Delivered). Kurir tidak boleh loncat status; hanya status
     * berikutnya yang valid dari status saat ini yang diterima.
     *
     * @throws \Illuminate\Validation\ValidationException kalau status yang diminta bukan urutan berikutnya yang valid.
     */
    public function updateStatus(Shipment $shipment, array $data): ShipmentTracking
    {
        return DB::transaction(function () use ($shipment, $data) {
            // Lock baris shipment supaya dua update status barengan (mis.
            // klik ganda / dua tab) tidak lolos berdua-duanya jadi status
            // yang sama, mencegah race condition pada validasi urutan.
            $current = Shipment::whereKey($shipment->id)->lockForUpdate()->first();

            $requested = ShipmentStatus::from($data['status']);
            $expected = $current->status->next();

            if ($expected === null || $requested !== $expected) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' => $expected
                        ? "Status harus diperbarui berurutan. Status berikutnya yang valid: {$expected->label()}."
                        : 'Shipment ini sudah pada status akhir dan tidak bisa diperbarui lagi.',
                ]);
            }

            $tracking = ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'status' => $data['status'],
                'location' => $data['location'] ?? null,
                'description' => $data['description'],
                'tracked_at' => now(),
            ]);
            // ShipmentTracking::booted() otomatis sync status ke shipment
            // induk & fire event ShipmentStatusUpdated.

            return $tracking;
        });
    }

    /**
     * Alur kasir (mirip J&T): begitu pembayaran dikonfirmasi/direkap
     * kasir, paket otomatis "diproses" di cabang lalu langsung ditugaskan
     * ke KURIR AKTIF berikutnya secara BERGANTIAN (round-robin) — kasir
     * gak perlu pilih manual satu-satu. Dipanggil dari:
     * - Api/Payment/PaymentController::verify() (kasir konfirmasi cash)
     * - MidtransWebhookController (transfer/e-wallet lunas otomatis)
     */
    public function processAtCounter(Shipment $shipment): Shipment
    {
        return DB::transaction(function () use ($shipment) {
            if ($shipment->courier_id) {
                return $shipment; // sudah ada kurir, jangan di-assign ulang
            }

            $courier = $this->pickNextAvailableCourier($shipment->origin_branch_id);

            if ($courier) {
                $shipment->update(['courier_id' => $courier->id]);
            }

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'status' => ShipmentStatus::PickedUp->value,
                'location' => $shipment->originBranch->city ?? null,
                'description' => $courier
                    ? "Pembayaran dikonfirmasi. Paket sedang dikemas & ditugaskan ke kurir {$courier->name}."
                    : 'Pembayaran dikonfirmasi. Paket sedang dikemas, menunggu kurir tersedia.',
                'tracked_at' => now(),
            ]);

            return $shipment->fresh();
        });
    }

    /**
     * Round-robin sederhana: pilih kurir di cabang asal yang PALING
     * SEDIKIT sedang pegang shipment aktif (belum delivered/cancelled).
     * Ini bikin beban kerja kurir merata bergantian, bukan numpuk ke 1 orang.
     */
    private function pickNextAvailableCourier(int $branchId): ?User
    {
        return User::role('courier')
            ->where('branch_id', $branchId)
            ->withCount(['courierShipments as active_shipments_count' => function ($q) {
                $q->whereNotIn('status', ['delivered', 'cancelled']);
            }])
            ->orderBy('active_shipments_count')
            ->first();
    }
}
