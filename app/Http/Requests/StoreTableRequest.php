<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            // unique app-level (skema tidak diubah) — mencegah meja kembar.
            'name'   => ['required', 'string', 'max:50', 'unique:tables,name'],
            'is_vip' => ['required', 'boolean'], // BR-015 (additive Modul 6)
        ];
    }

    protected function prepareForValidation(): void
    {
        // Toggle/checkbox → pastikan is_vip selalu boolean.
        $this->merge([
            'is_vip' => $this->boolean('is_vip'),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama meja wajib diisi.',
            'name.max'      => 'Nama meja maksimal 50 karakter.',
            'name.unique'   => 'Nama meja sudah digunakan.',
        ];
    }
}
