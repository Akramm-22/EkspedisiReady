<?php

namespace App\Http\Requests\Shipment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin|manager|cashier|courier');
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:picked_up,in_transit,arrived_at_branch,out_for_delivery,delivered,cancelled'],
            'location' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:255'],
        ];
    }
}
