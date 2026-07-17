<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case Pending = 'pending';
    case PickedUp = 'picked_up';
    case InTransit = 'in_transit';
    case ArrivedAtBranch = 'arrived_at_branch';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Pickup',
            self::PickedUp => 'Sudah Diambil Kurir',
            self::InTransit => 'Dalam Perjalanan',
            self::ArrivedAtBranch => 'Tiba di Cabang',
            self::OutForDelivery => 'Sedang Diantar',
            self::Delivered => 'Terkirim',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /** Urutan linear alur normal, dipakai untuk render timeline publik. */
    public static function timelineOrder(): array
    {
        return [
            self::Pending,
            self::PickedUp,
            self::InTransit,
            self::ArrivedAtBranch,
            self::OutForDelivery,
            self::Delivered,
        ];
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled], true);
    }

    /** Status berikutnya yang valid dalam alur linear normal. Null kalau sudah di akhir/final. */
    public function next(): ?self
    {
        $order = self::timelineOrder();
        $index = array_search($this, $order, true);

        if ($index === false || ! isset($order[$index + 1])) {
            return null;
        }

        return $order[$index + 1];
    }
}
