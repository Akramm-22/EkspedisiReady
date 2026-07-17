<?php

namespace App\Http\Requests\Rate;

use Illuminate\Foundation\Http\FormRequest;

class StoreRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'origin_city' => ['required', 'string', 'max:255'],
            'destination_city' => ['required', 'string', 'max:255'],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'estimated_days' => ['required', 'integer', 'min:0'],
        ];
    }
}
