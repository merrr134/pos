<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'   => [
                'required', 'string', 'max:50',
                Rule::unique('tables', 'name')->ignore($this->route('table')),
            ],
            'is_vip' => ['required', 'boolean'], // BR-015 (additive Modul 6)
        ];
    }

    protected function prepareForValidation(): void
    {
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
