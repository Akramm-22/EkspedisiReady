<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')?->id;

        return [
            'plate_number' => ['required', 'string', 'max:255', 'unique:vehicles,plate_number,'.$vehicleId],
            'type' => ['required', 'in:motor,mobil,truck'],
            'courier_id' => ['required', 'exists:users,id'],
        ];
    }
}
