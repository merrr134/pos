<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:100', 'unique:categories,name'],
            'station' => ['required', Rule::in(['kitchen', 'barista'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Nama kategori wajib diisi.',
            'name.max'         => 'Nama kategori maksimal 100 karakter.',
            'name.unique'      => 'Nama kategori sudah digunakan.',
            'station.required' => 'Station wajib dipilih.',
            'station.in'       => 'Station harus Kitchen atau Barista.',
        ];
    }
}
