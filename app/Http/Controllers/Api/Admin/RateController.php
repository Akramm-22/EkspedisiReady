<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rate\StoreRateRequest;
use App\Models\Rate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rates = Rate::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('origin_city', 'like', "%{$request->search}%")
                    ->orWhere('destination_city', 'like', "%{$request->search}%");
            })
            ->paginate($request->integer('per_page', 25));

        return response()->json(['data' => $rates]);
    }

    public function store(StoreRateRequest $request): JsonResponse
    {
        $rate = Rate::create($request->validated());

        return response()->json(['message' => 'Tarif berhasil dibuat.', 'data' => $rate], 201);
    }

    public function update(StoreRateRequest $request, Rate $rate): JsonResponse
    {
        $rate->update($request->validated());

        return response()->json(['message' => 'Tarif berhasil diperbarui.', 'data' => $rate->fresh()]);
    }

    public function destroy(Rate $rate): JsonResponse
    {
        $this->authorize('manage', $rate);

        $rate->delete();

        return response()->json(['message' => 'Tarif berhasil dihapus.']);
    }
}
