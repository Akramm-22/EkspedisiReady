<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, MustVerifyEmailTrait, Notifiable;

    /**
     * Sesuai PDM: customer adalah entitas auth TERPISAH dari `users`
     * (punya password & email_verified_at sendiri). Login lewat guard
     * `customer` (lihat config/auth.php), bukan guard `web` yang dipakai
     * staf internal (admin/cashier/courier/manager).
     */
    protected $fillable = ['name', 'email', 'password', 'address', 'city', 'phone', 'photo'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Shipment yang customer ini kirim (sebagai pengirim). */
    public function sentShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'sender_id');
    }

    /** Shipment yang customer ini terima (sebagai penerima). */
    public function receivedShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'receiver_id');
    }
}
