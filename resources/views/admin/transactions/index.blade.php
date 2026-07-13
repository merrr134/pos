@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6">
        <h2 class="font-display text-2xl font-bold">Riwayat Transaksi</h2>
        <p class="mt-1 text-sm text-white/80">Seluruh pembayaran yang sudah selesai. Read-only — nilai memakai snapshot saat transaksi.</p>
    </div>

    {{-- Ringkasan hasil filter --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand/10 text-brand">
                    <x-lucide-receipt class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Transaksi</p>
                    <p class="text-xl font-bold text-slate-800">{{ $stats->total }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                    <x-lucide-wallet class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Pendapatan</p>
                    <p class="text-xl font-bold text-slate-800">Rp{{ number_format($stats->revenue, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                    <x-lucide-percent class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Pajak</p>
                    <p class="text-xl font-bold text-slate-800">Rp{{ number_format($stats->tax, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu tabel --}}
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">

        {{-- Toolbar filter (GET) --}}
        <form method="GET" action="{{ route('admin.transactions.index') }}"
              class="flex flex-col gap-3 border-b border-slate-100 p-4 lg:flex-row lg:items-end">
            <div class="min-w-0 flex-1">
                <label for="search" class="mb-1 block text-xs font-medium text-slate-500">Cari</label>
                <div class="relative">
                    <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" id="search" name="search" value="{{ $search }}"
                           placeholder="Nomor order atau nama customer..."
                           class="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-brand focus:ring-brand" />
                </div>
            </div>
            <div>
                <label for="date_from" class="mb-1 block text-xs font-medium text-slate-500">Dari Tanggal</label>
                <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}"
                       class="rounded-lg border border-slate-200 py-2 px-3 text-sm focus:border-brand focus:ring-brand" />
            </div>
            <div>
                <label for="date_to" class="mb-1 block text-xs font-medium text-slate-500">Sampai Tanggal</label>
                <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}"
                       class="rounded-lg border border-slate-200 py-2 px-3 text-sm focus:border-brand focus:ring-brand" />
            </div>
            <div>
                <label for="method" class="mb-1 block text-xs font-medium text-slate-500">Metode</label>
                <select id="method" name="method"
                        class="rounded-lg border border-slate-200 py-2 pl-3 pr-8 text-sm focus:border-brand focus:ring-brand">
                    <option value="">Semua</option>
                    <option value="cash" @selected($method === 'cash')>Cash</option>
                    <option value="qris" @selected($method === 'qris')>QRIS</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white hover:bg-brand-light">
                    Filter
                </button>
                @if ($search || $method || $dateFrom || $dateTo)
                    <a href="{{ route('admin.transactions.index') }}"
                       class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">No. Order</th>
                        <th class="px-4 py-3 font-medium">Customer</th>
                        <th class="px-4 py-3 font-medium">Meja</th>
                        <th class="px-4 py-3 font-medium">Metode</th>
                        <th class="px-4 py-3 text-right font-medium">Subtotal</th>
                        <th class="px-4 py-3 text-right font-medium">Pajak</th>
                        <th class="px-4 py-3 text-right font-medium">Grand Total</th>
                        <th class="px-4 py-3 font-medium">Kasir</th>
                        <th class="px-4 py-3 font-medium">Waktu</th>
                        <th class="px-4 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $payment)
                        <tr class="text-slate-700">
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $payment->order->order_number }}</td>
                            <td class="px-4 py-3">{{ $payment->order->customer_name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $payment->order->table->name }}</td>
                            <td class="px-4 py-3">
                                @if ($payment->payment_method === 'qris')
                                    <span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-semibold text-sky-700">QRIS</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Cash</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-slate-500">Rp{{ number_format($payment->order->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-slate-500">Rp{{ number_format($payment->tax_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-800">Rp{{ number_format($payment->amount_paid - $payment->change, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $payment->receiver->name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-500">{{ $payment->paid_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.transactions.show', $payment) }}"
                                       class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-brand" title="Detail">
                                        <x-lucide-eye class="h-4 w-4" />
                                    </a>
                                    <a href="{{ route('admin.transactions.invoice', $payment) }}" target="_blank"
                                       class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-brand" title="Cetak Invoice PDF">
                                        <x-lucide-file-text class="h-4 w-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center">
                                <x-lucide-receipt class="mx-auto h-8 w-8 text-slate-200" />
                                <p class="mt-2 text-sm text-slate-400">
                                    {{ ($search || $method || $dateFrom || $dateTo) ? 'Tidak ada transaksi yang cocok dengan filter.' : 'Belum ada transaksi.' }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: info + pagination --}}
        <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-400">
                Menampilkan {{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }} dari {{ $payments->total() }} transaksi
            </p>
            <div>{{ $payments->onEachSide(1)->links() }}</div>
        </div>
    </div>
@endsection
