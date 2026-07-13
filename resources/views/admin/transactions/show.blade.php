@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('admin.transactions.index') }}"
               class="mb-3 inline-flex items-center gap-1.5 text-sm text-white/70 hover:text-white">
                <x-lucide-arrow-left class="h-4 w-4" /> Kembali ke Riwayat Transaksi
            </a>
            <h2 class="font-display text-2xl font-bold">Detail Transaksi</h2>
            <p class="mt-1 text-sm text-white/80">{{ $payment->order->order_number }} · {{ $invoiceNumber }}</p>
        </div>
        <a href="{{ route('admin.transactions.invoice', $payment) }}" target="_blank"
           class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-brand shadow-sm hover:bg-white/90">
            <x-lucide-file-text class="h-4 w-4" /> Cetak Invoice
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Kolom kiri: header info + item --}}
        <div class="space-y-6 xl:col-span-2">
            {{-- Header transaksi --}}
            <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs text-slate-400">Nomor Order</p>
                        <p class="mt-0.5 text-sm font-semibold text-slate-800">{{ $payment->order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Customer</p>
                        <p class="mt-0.5 text-sm font-semibold text-slate-800">{{ $payment->order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Meja</p>
                        <p class="mt-0.5 text-sm font-semibold text-slate-800">{{ $payment->order->table->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Kasir</p>
                        <p class="mt-0.5 text-sm font-semibold text-slate-800">{{ $payment->receiver->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Metode Pembayaran</p>
                        <p class="mt-0.5">
                            @if ($payment->payment_method === 'qris')
                                <span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-semibold text-sky-700">QRIS</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Cash</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Tanggal</p>
                        <p class="mt-0.5 text-sm font-semibold text-slate-800">{{ $payment->paid_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Status</p>
                        <p class="mt-0.5">
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                <x-lucide-check class="h-3 w-3" /> Lunas
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Daftar item (snapshot harga di order_items) --}}
            <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-semibold text-slate-800">Daftar Item</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-medium">Menu</th>
                                <th class="px-5 py-3 text-center font-medium">Qty</th>
                                <th class="px-5 py-3 text-right font-medium">Harga</th>
                                <th class="px-5 py-3 text-right font-medium">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($payment->order->items as $item)
                                <tr class="text-slate-700">
                                    <td class="px-5 py-3 font-medium text-slate-800">{{ $item->menu->name }}</td>
                                    <td class="px-5 py-3 text-center">{{ $item->qty }}</td>
                                    <td class="px-5 py-3 text-right text-slate-500">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-right font-semibold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kolom kanan: ringkasan pembayaran (SNAPSHOT payment) --}}
        <div>
            @php($taxLabel = rtrim(rtrim(number_format($payment->tax_percent, 2, ',', '.'), '0'), ','))
            @php($grandTotal = (float) $payment->amount_paid - (float) $payment->change)
            <div class="sticky top-6 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Ringkasan Pembayaran</h3>
                <div class="space-y-2.5 text-sm">
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($payment->order->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Pajak ({{ $taxLabel }}%)</span>
                        <span>Rp{{ number_format($payment->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-dashed border-slate-200 pt-2.5">
                        <span class="font-semibold text-slate-800">Grand Total</span>
                        <span class="text-lg font-bold text-brand">Rp{{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($payment->payment_method === 'qris')
                        {{-- BR-017: QRIS selalu pas --}}
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Metode Pembayaran</span>
                            <span class="font-semibold text-slate-700">QRIS</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Status Pembayaran</span>
                            <span class="font-bold text-emerald-600">LUNAS</span>
                        </div>
                    @else
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Nominal Bayar</span>
                            <span>Rp{{ number_format($payment->amount_paid, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Kembalian</span>
                            <span>Rp{{ number_format($payment->change, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <a href="{{ route('admin.transactions.invoice', $payment) }}" target="_blank"
                   class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-light">
                    <x-lucide-file-text class="h-4 w-4" /> Cetak Invoice (PDF)
                </a>
                <p class="mt-2 text-center text-[11px] text-slate-400">
                    Invoice memakai nilai snapshot — aman dicetak ulang kapan saja.
                </p>
            </div>
        </div>
    </div>
@endsection
