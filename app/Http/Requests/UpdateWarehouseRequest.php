<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $warehouseId = $this->route('warehouse')?->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('warehouses', 'name')->ignore($warehouseId)],
            'code' => ['required', 'string', 'max:50', Rule::unique('warehouses', 'code')->ignore($warehouseId)],
            'address' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
