<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /** GET /api/v1/admin/couriers — daftar kurir untuk dropdown assign-courier. */
    public function index(Request $request): JsonResponse
    {
        $couriers = User::query()
            ->role('courier')
            ->withCount([
                'courierShipments as active_shipments_count' => fn ($q) => $q->whereNotIn('status', ['delivered', 'cancelled']),
            ])
            ->get(['id', 'name', 'branch_id']);

        return response()->json(['data' => $couriers]);
    }
}
