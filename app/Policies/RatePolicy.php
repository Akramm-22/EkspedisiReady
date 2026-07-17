<?php

namespace App\Policies;

use App\Models\Rate;
use App\Models\User;

class RatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function manage(User $user, Rate $rate): bool
    {
        return $user->hasRole('admin');
    }
}
