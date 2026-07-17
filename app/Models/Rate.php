<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rate extends Model
{
    protected $fillable = ['origin_city', 'destination_city', 'price_per_kg', 'estimated_days'];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    /** Hitung ongkir untuk berat tertentu. */
    public function calculate(float $weightKg): float
    {
        return round($weightKg * $this->price_per_kg, 2);
    }

    public function scopeRoute($query, string $origin, string $destination)
    {
        return $query->where('origin_city', $origin)->where('destination_city', $destination);
    }
}
