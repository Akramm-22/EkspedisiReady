<?php

namespace App\Http\Requests\Shipment;

use Illuminate\Foundation\Http\FormRequest;

class AssignCourierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin|manager|cashier');
    }

    public function rules(): array
    {
        return [
            'courier_id' => ['required', 'exists:users,id'],
        ];
    }
}
