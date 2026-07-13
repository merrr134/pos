<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya Admin yang boleh mengubah pengaturan (BR-016).
        return $this->user()?->isRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'tax_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'tax_percent.required' => 'Persentase pajak wajib diisi.',
            'tax_percent.numeric'  => 'Persentase pajak harus berupa angka.',
            'tax_percent.min'      => 'Persentase pajak tidak boleh negatif.',
            'tax_percent.max'      => 'Persentase pajak maksimal 100%.',
        ];
    }
}
