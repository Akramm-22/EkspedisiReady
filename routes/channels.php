<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Tracking status shipment (ShipmentStatusUpdated) di-broadcast sebagai
| Channel publik (bukan PrivateChannel) karena datanya memang untuk
| ditampilkan ke siapa saja yang tahu nomor resi — tidak perlu otorisasi
| di sini.
*/
