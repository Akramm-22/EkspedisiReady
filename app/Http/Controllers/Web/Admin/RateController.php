<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rate\StoreRateRequest;
use App\Models\Rate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RateController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Rate::class);

        $rates = Rate::latest()->paginate(20);

        return view('admin.rates.index', compact('rates'));
    }

    public function store(StoreRateRequest $request): RedirectResponse
    {
        Rate::create($request->validated());

        return back()->with('success', 'Tarif berhasil ditambahkan.');
    }

    public function update(StoreRateRequest $request, Rate $rate): RedirectResponse
    {
        $rate->update($request->validated());

        return back()->with('success', 'Tarif berhasil diperbarui.');
    }

    public function destroy(Rate $rate): RedirectResponse
    {
        $this->authorize('manage', $rate);

        $rate->delete();

        return back()->with('success', 'Tarif berhasil dihapus.');
    }
}
