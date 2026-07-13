<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isRole('waiters') ?? false;
    }

    public function rules(): array
    {
        return [
            'table_id'        => ['required', 'integer', 'exists:tables,id'],
            'customer_name'   => ['required', 'string', 'max:100'], // wajib (FR-005) — SRS menang atas Figma "opsional"
            'items'           => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', 'distinct', 'exists:menus,id'],
            'items.*.qty'     => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'table_id.required'        => 'Pilih meja terlebih dahulu.',
            'table_id.exists'          => 'Meja tidak valid.',
            'customer_name.required'   => 'Nama pelanggan wajib diisi.',
            'customer_name.max'        => 'Nama pelanggan maksimal 100 karakter.',
            'items.required'           => 'Order minimal berisi 1 menu.',
            'items.min'                => 'Order minimal berisi 1 menu.',
            'items.*.menu_id.required' => 'Menu tidak valid.',
            'items.*.menu_id.distinct' => 'Ada menu yang duplikat di dalam order.',
            'items.*.menu_id.exists'   => 'Menu tidak valid.',
            'items.*.qty.required'     => 'Quantity wajib diisi.',
            'items.*.qty.integer'      => 'Quantity harus berupa angka.',
            'items.*.qty.min'          => 'Quantity minimal 1.',
        ];
    }
}
