<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'type' => ['required', 'string', 'in:in,out,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'movement_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Tipe pergerakan harus salah satu dari: masuk (in), keluar (out), atau penyesuaian (adjustment).',
            'quantity.min' => 'Jumlah harus minimal 1.',
            'product_id.exists' => 'Produk tidak ditemukan.',
            'warehouse_id.exists' => 'Gudang tidak ditemukan.',
        ];
    }
}
