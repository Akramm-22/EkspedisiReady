<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Rate;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Public/Home', [
            'home' => [
                'rates' => Rate::orderBy('origin_city')->get(['id', 'origin_city', 'destination_city', 'price_per_kg', 'estimated_days']),
                'stats' => Cache::remember('landing:live-stats', now()->addMinutes(5), fn () => [
                    'total_shipments' => Shipment::count(),
                    'total_branches' => Branch::count(),
                    'delivered_this_month' => Shipment::where('status', 'delivered')
                        ->whereMonth('updated_at', now()->month)->count(),
                ]),
            ],
        ]);
    }

    public function track(Request $request): Response
    {
        return Inertia::render('Public/Track', [
            'tracking_number' => $request->query('tracking_number', ''),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('Public/About', [
            'branches' => Branch::all(),
        ]);
    }
}
