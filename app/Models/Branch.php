<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = ['name', 'city', 'address', 'phone'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function originShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'origin_branch_id');
    }

    public function destinationShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'destination_branch_id');
    }
}
