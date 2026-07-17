<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Paid = 'paid';
    case Failed = 'failed';
    case Expired = 'expired';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Pembayaran',
            self::Processing => 'Diproses',
            self::Paid => 'Lunas',
            self::Failed => 'Gagal',
            self::Expired => 'Kadaluarsa',
            self::Refunded => 'Dikembalikan',
        };
    }
}
