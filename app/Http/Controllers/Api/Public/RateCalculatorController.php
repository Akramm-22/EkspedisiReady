<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RateCalculatorController extends Controller
{
    /** POST /api/rates/calculate — kalkulator tarif publik. */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'origin_city' => ['required', 'string', 'max:100'],
            'destination_city' => ['required', 'string', 'max:100'],
            'weight_kg' => ['required', 'numeric', 'min:0.1', 'max:1000'],
        ]);

        $cacheKey = 'rates:'.str($validated['origin_city'])->lower()->slug().':'.str($validated['destination_city'])->lower()->slug();

        $rate = Cache::remember($cacheKey, now()->addHour(), function () use ($validated) {
            return Rate::route($validated['origin_city'], $validated['destination_city'])->first();
        });

        if (! $rate) {
            return response()->json([
                'message' => 'Rute pengiriman ini belum tersedia. Silakan hubungi cabang terdekat.',
                'data' => null,
            ], 404);
        }

        return response()->json(['data' => [
            'price' => $rate->calculate((float) $validated['weight_kg']),
            'estimated_days' => $rate->estimated_days,
        ]]);
    }
}
