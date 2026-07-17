<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Staf internal (admin/cashier/courier/manager). Sesuai PDM, role
     * adalah kolom enum sederhana di tabel `users` — bukan Spatie
     * Permission. Customer adalah entitas terpisah (lihat App\Models\Customer),
     * bukan bagian dari enum role ini.
     */
    protected $fillable = ['name', 'email', 'password', 'role', 'branch_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function courierShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'courier_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'courier_id');
    }

    /** Pengganti ringan Spatie hasRole() — cocokkan terhadap kolom `role`. */
    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : explode('|', $roles);

        return in_array($this->role, array_map('strtolower', $roles), true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isCourier(): bool
    {
        return $this->role === 'courier';
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
