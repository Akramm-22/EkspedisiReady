<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // semua role staf boleh lihat daftar (dibatasi query per-role di controller)
    }

    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->isCourier()) {
            return $shipment->courier_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin|cashier');
    }

    /** Update status pengiriman HANYA boleh dilakukan oleh kurir yang ditugaskan.
     * Admin/manager/cashier cuma bisa lihat (read-only) — status berjalan
     * otomatis mengikuti aksi kurir di lapangan. */
    public function update(User $user, Shipment $shipment): bool
    {
        return $user->isCourier() && $shipment->courier_id === $user->id;
    }

    public function assignCourier(User $user, Shipment $shipment): bool
    {
        return $user->hasRole('admin|cashier');
    }

    public function delete(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin() && $shipment->status->value === 'pending';
    }
}
