<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:150'],
            'category_id'  => ['required', 'exists:categories,id'],
            'description'  => ['nullable', 'string'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'price'        => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'is_available' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Toggle/checkbox → pastikan is_available selalu boolean.
        $this->merge([
            'is_available' => $this->boolean('is_available'),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Nama menu wajib diisi.',
            'name.max'             => 'Nama menu maksimal 150 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak valid.',
            'image.image'          => 'File harus berupa gambar.',
            'image.mimes'          => 'Format gambar harus JPG, PNG, atau WEBP.',
            'image.max'            => 'Ukuran gambar maksimal 2 MB.',
            'price.required'       => 'Harga wajib diisi.',
            'price.numeric'        => 'Harga harus berupa angka.',
            'price.min'            => 'Harga tidak boleh negatif.',
        ];
    }
}
