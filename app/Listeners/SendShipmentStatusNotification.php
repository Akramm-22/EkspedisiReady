<?php

namespace App\Listeners;

use App\Events\ShipmentStatusUpdated;

/**
 * PDM belum punya tabel notifikasi/push khusus, jadi untuk sekarang
 * kirim email standar Laravel Notification ke customer (guard `customer`)
 * memakai mailer bawaan. Dijalankan SYNCHRONOUS (bukan ShouldQueue) supaya
 * tidak butuh tabel `jobs` — kalau nanti butuh proses berat/async, pasang
 * `ShouldQueue` lagi setelah `QUEUE_CONNECTION` di-set ke driver yang
 * benar-benar punya backing store (database/redis) dan tabelnya sudah dibuat.
 */
class SendShipmentStatusNotification
{
    public function handle(ShipmentStatusUpdated $event): void
    {
        $shipment = $event->tracking->shipment()->with('sender')->first();

        // Notification::send($shipment->sender, new ShipmentStatusMail($event->tracking));
        // ^ Placeholder — buat Notification class kalau butuh isi emailnya.
    }
}
