<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isRole('kasir') ?? false;
    }

    public function rules(): array
    {
        return [
            'order_id'       => ['required', 'integer', 'exists:orders,id'],
            'payment_method' => ['required', Rule::in(['cash', 'qris'])], // sesuai enum skema
            // BR-017: saat QRIS, amount_paid DIBUANG dari data tervalidasi (exclude_if) —
            // server pasti memakai grand total, client tidak bisa menentukan nominal QRIS.
            // Saat cash: wajib. BR-007 divalidasi di controller terhadap total DB.
            'amount_paid'    => ['exclude_if:payment_method,qris', 'required', 'numeric', 'min:0', 'max:9999999999'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required'       => 'Pilih order yang akan dibayar.',
            'order_id.exists'         => 'Order tidak valid.',
            'payment_method.required' => 'Pilih metode pembayaran.',
            'payment_method.in'       => 'Metode pembayaran harus Cash atau QRIS.',
            'amount_paid.required'    => 'Nominal bayar wajib diisi.',
            'amount_paid.numeric'     => 'Nominal bayar harus berupa angka.',
            'amount_paid.min'         => 'Nominal bayar tidak boleh negatif.',
        ];
    }
}
