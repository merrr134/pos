<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * FR-009 — Riwayat Transaksi Admin + Invoice PDF. READ-ONLY.
 * Sumber data = payments (otomatis hanya order yang sudah dibayar); semua nilai
 * dari SNAPSHOT payment (tax_percent/tax_amount/amount_paid/change) — TIDAK PERNAH
 * membaca Setting untuk nilai finansial, sehingga reprint kebal perubahan pajak.
 */
class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $search   = trim((string) $request->input('search'));
        $method   = $request->input('method');     // '' | cash | qris
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $base = Payment::query()
            ->when($dateFrom, fn ($q, $d) => $q->whereDate('paid_at', '>=', $d))
            ->when($dateTo, fn ($q, $d) => $q->whereDate('paid_at', '<=', $d))
            ->when(in_array($method, ['cash', 'qris'], true), fn ($q) => $q->where('payment_method', $method))
            ->when($search, fn ($q, $s) => $q->whereHas('order', function ($o) use ($s) {
                // Satu field search untuk nomor order ATAU customer (keputusan user).
                $o->where('order_number', 'like', "%{$s}%")
                    ->orWhere('customer_name', 'like', "%{$s}%");
            }));

        // Ringkasan hasil filter — 1 query agregat, terpisah dari pagination.
        $stats = (clone $base)->selectRaw(
            'COUNT(*) as total,
             COALESCE(SUM(amount_paid - `change`), 0) as revenue,
             COALESCE(SUM(tax_amount), 0) as tax'
        )->first();

        $payments = $base
            ->with(['order.table', 'receiver']) // eager — tanpa N+1
            ->latest('paid_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.transactions.index', compact('payments', 'stats', 'search', 'method', 'dateFrom', 'dateTo'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['order.table', 'order.items.menu', 'receiver']);

        return view('admin.transactions.show', [
            'payment'       => $payment,
            'invoiceNumber' => $this->invoiceNumber($payment),
        ]);
    }

    /**
     * Invoice PDF A4 (DomPDF) — boleh dicetak ulang kapan saja.
     * Order belum dibayar tidak punya baris payment → route binding otomatis 404.
     */
    public function invoice(Payment $payment)
    {
        $payment->load(['order.table', 'order.items.menu', 'receiver']);
        $invoiceNumber = $this->invoiceNumber($payment);

        return Pdf::loadView('admin.transactions.invoice', [
            'payment'       => $payment,
            'invoiceNumber' => $invoiceNumber,
            // Identitas toko (bukan nilai finansial) — kelak diatur Modul Settings tanpa mengubah kode ini.
            'storeAddress'  => Setting::get('store_address', 'Jl. Kopi Nusantara No. 1, Indonesia'),
        ])->stream("{$invoiceNumber}.pdf");
    }

    /** Nomor invoice deterministik (disetujui user): stabil selamanya untuk reprint, tanpa kolom baru. */
    private function invoiceNumber(Payment $payment): string
    {
        return 'INV-' . $payment->paid_at->format('Ymd') . '-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT);
    }
}
