<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Rate;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class LandingPageController extends Controller
{
    /** GET /api/home — payload untuk halaman utama (statistik + daftar rute tarif). */
    public function home(): JsonResponse
    {
        return response()->json([
            'data' => [
                'rates' => Rate::orderBy('origin_city')->get(['id', 'origin_city', 'destination_city', 'price_per_kg', 'estimated_days']),
                'stats' => $this->liveStats(),
            ],
        ]);
    }

    /** GET /api/branches — daftar cabang untuk halaman "Tentang Kami". */
    public function branches(): JsonResponse
    {
        return response()->json(['data' => Branch::all(['id', 'name', 'city', 'address', 'phone'])]);
    }

    private function liveStats(): array
    {
        return Cache::remember('landing:live-stats', now()->addMinutes(5), function () {
            return [
                'total_shipments' => Shipment::count(),
                'total_branches' => Branch::count(),
                'delivered_this_month' => Shipment::where('status', 'delivered')
                    ->whereMonth('updated_at', now()->month)
                    ->count(),
            ];
        });
    }
}
