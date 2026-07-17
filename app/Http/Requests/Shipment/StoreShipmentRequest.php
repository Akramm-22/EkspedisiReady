<?php

namespace App\Http\Requests\Shipment;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin|manager|cashier');
    }

    public function rules(): array
    {
        return [
            'sender_id' => ['required', 'exists:customers,id'],
            'receiver_id' => ['required', 'exists:customers,id'],

            'origin_branch_id' => ['required', 'exists:branches,id'],
            'destination_branch_id' => ['required', 'exists:branches,id'],
            'courier_id' => ['nullable', 'exists:users,id'],
            'rate_id' => ['required', 'exists:rates,id'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:150'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.weight' => ['required', 'numeric', 'min:0.1'],
            'items.*.photo' => ['nullable', 'string'],

            'photo' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
