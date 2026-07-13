<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Proses pembayaran kasir (FR-008) — satu transaksi:
     * lock order → BR-013 (harus aktif) → BR-007 (bayar >= total DB) → simpan payment
     * (BR-011 dijaga UNIQUE payments.order_id) → BR-004/BR-005: order paid + meja kosong.
     */
    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data) {
            $order = Order::with('table')->whereKey($data['order_id'])->lockForUpdate()->firstOrFail();

            // BR-013: kasir hanya bisa memproses order aktif (sekaligus BR-011 lapis pertama).
            if ($order->status !== 'active') {
                throw ValidationException::withMessages([
                    'order_id' => "Order {$order->order_number} sudah dibayar dan tidak bisa diproses lagi.",
                ]);
            }

            // BR-016: pajak dinamis dari Settings — dihitung SERVER-SIDE, dibulatkan ke rupiah.
            $taxPercent = Setting::taxPercent();
            $taxAmount  = round((float) $order->total * $taxPercent / 100);
            $grandTotal = (float) $order->total + $taxAmount;

            // BR-017: QRIS selalu tepat sebesar grand total — nominal dari client DIABAIKAN
            // (exclude_if di Form Request sudah membuangnya), kembalian selalu 0.
            $isQris     = $data['payment_method'] === 'qris';
            $amountPaid = $isQris ? $grandTotal : (float) $data['amount_paid'];

            // BR-007 (khusus cash): nominal bayar >= GRAND TOTAL — terhadap nilai DB, bukan input client.
            if (! $isQris && $amountPaid < $grandTotal) {
                throw ValidationException::withMessages([
                    'amount_paid' => 'Nominal bayar kurang dari total setelah pajak (Rp' . number_format($grandTotal, 0, ',', '.') . ').',
                ]);
            }

            try {
                Payment::create([
                    'order_id'       => $order->id,
                    'amount_paid'    => $amountPaid,
                    'change'         => $amountPaid - $grandTotal, // cash: kembalian server; qris: selalu 0 (BR-017)
                    'tax_percent'    => $taxPercent,  // snapshot — struk lama tak berubah saat pajak diubah
                    'tax_amount'     => $taxAmount,   // snapshot
                    'payment_method' => $data['payment_method'],
                    'received_by'    => auth()->id(),        // kasir yang login (FR-008)
                    'paid_at'        => now(),               // penanda transaksi selesai (BR-005)
                ]);
            } catch (UniqueConstraintViolationException) {
                // BR-011 lapis kedua: UNIQUE payments.order_id — race dua kasir/tab.
                throw ValidationException::withMessages([
                    'order_id' => "Order {$order->order_number} sudah dibayar dan tidak bisa diproses lagi.",
                ]);
            }

            // BR-004: order selesai, meja kembali kosong.
            $order->update(['status' => 'paid']);
            $order->table->update(['status' => 'kosong']);

            return $order;
        });

        return redirect()
            ->route('cashier.index', ['paid' => $order->id])
            ->with('success', "Pembayaran {$order->order_number} berhasil. {$order->table->name} kini kosong.");
    }

    /**
     * Struk thermal 58mm (FR-008/FR-009) — hanya untuk order yang sudah dibayar.
     * Boleh dicetak ulang kapan saja (keputusan user — TIDAK mengikuti BR-014 checker).
     */
    public function receipt(Order $order): View
    {
        abort_unless($order->status === 'paid' && $order->payment()->exists(), 404);

        return view('cashier.receipt', [
            'order' => $order->load(['table', 'items.menu', 'payment.receiver']),
        ]);
    }
}
