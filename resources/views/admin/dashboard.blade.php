@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6">
        <h2 class="font-display text-2xl font-bold">Dashboard Admin</h2>
        <p class="mt-1 text-sm text-white/80">Ringkasan operasional Pitou Cafe — seluruh angka dihitung dari transaksi yang sudah dibayar (termasuk pajak).</p>
    </div>

    {{-- ===== Baris 1: 4 kartu utama ===== --}}
    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand/10 text-brand">
                <x-lucide-wallet class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Pendapatan Hari Ini</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">Rp{{ number_format($today->revenue, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand/10 text-brand">
                <x-lucide-trending-up class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Pendapatan Bulan Ini</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">Rp{{ number_format($month->revenue, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                <x-lucide-receipt class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Order Hari Ini</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $today->orders }}</p>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                <x-lucide-clipboard-list class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Order Aktif</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $counts['active_orders'] }}</p>
        </div>
    </div>

    {{-- ===== Strip mini card ===== --}}
    <div class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-4">
        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white px-4 py-3 shadow-sm">
            <x-lucide-calendar-days class="h-4 w-4 shrink-0 text-slate-400" />
            <div class="flex w-full items-center justify-between gap-2">
                <span class="text-xs text-slate-500">Order Bulan Ini</span>
                <span class="text-sm font-bold text-slate-800">{{ $month->orders }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white px-4 py-3 shadow-sm">
            <x-lucide-utensils-crossed class="h-4 w-4 shrink-0 text-slate-400" />
            <div class="flex w-full items-center justify-between gap-2">
                <span class="text-xs text-slate-500">Total Menu</span>
                <span class="text-sm font-bold text-slate-800">{{ $counts['menus'] }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white px-4 py-3 shadow-sm">
            <x-lucide-armchair class="h-4 w-4 shrink-0 text-slate-400" />
            <div class="flex w-full items-center justify-between gap-2">
                <span class="text-xs text-slate-500">Total Meja</span>
                <span class="text-sm font-bold text-slate-800">{{ $counts['tables'] }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white px-4 py-3 shadow-sm">
            <x-lucide-users class="h-4 w-4 shrink-0 text-slate-400" />
            <div class="flex w-full items-center justify-between gap-2">
                <span class="text-xs text-slate-500">Total User</span>
                <span class="text-sm font-bold text-slate-800">{{ $counts['users'] }}</span>
            </div>
        </div>
    </div>

    {{-- ===== Baris 2: metode pembayaran (bulan ini) + pajak ===== --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                    <x-lucide-banknote class="h-5 w-5" />
                </div>
                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-600">{{ $methods['cash_percent'] }}%</span>
            </div>
            <p class="mt-4 text-sm text-slate-500">Cash Bulan Ini</p>
            <p class="mt-1 text-xl font-bold text-slate-800">Rp{{ number_format($methods['cash'], 0, ',', '.') }}</p>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 text-sky-600">
                    <x-lucide-qr-code class="h-5 w-5" />
                </div>
                <span class="rounded-full bg-sky-50 px-2 py-0.5 text-xs font-semibold text-sky-600">{{ $methods['qris_percent'] }}%</span>
            </div>
            <p class="mt-4 text-sm text-slate-500">QRIS Bulan Ini</p>
            <p class="mt-1 text-xl font-bold text-slate-800">Rp{{ number_format($methods['qris'], 0, ',', '.') }}</p>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                <x-lucide-percent class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Pajak Hari Ini</p>
            <p class="mt-1 text-xl font-bold text-slate-800">Rp{{ number_format($today->tax, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                <x-lucide-landmark class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Pajak Bulan Ini</p>
            <p class="mt-1 text-xl font-bold text-slate-800">Rp{{ number_format($month->tax, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- ===== Baris 3: grafik penjualan 7 hari ===== --}}
    <div class="mb-6 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-slate-800">Grafik Penjualan</h3>
                <p class="text-xs text-slate-400">Grand total pembayaran 7 hari terakhir (termasuk pajak)</p>
            </div>
            <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs text-slate-500">7 Hari</span>
        </div>

        @if (array_sum($chart['data']) > 0)
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        @else
            <div class="flex h-40 flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-200">
                <x-lucide-bar-chart-3 class="h-7 w-7 text-slate-200" />
                <p class="text-sm text-slate-400">Belum ada penjualan dalam 7 hari terakhir.</p>
            </div>
        @endif
    </div>

    {{-- ===== Baris 4: menu terlaris + aktivitas terbaru ===== --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Menu Terlaris --}}
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Menu Terlaris</h3>
            @if ($topMenus->isEmpty())
                <div class="flex h-40 flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-200">
                    <x-lucide-utensils-crossed class="h-7 w-7 text-slate-200" />
                    <p class="text-sm text-slate-400">Belum ada menu terjual.</p>
                </div>
            @else
                <ul class="space-y-4">
                    @foreach ($topMenus as $item)
                        <li class="flex items-center gap-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold {{ $loop->first ? 'bg-amber-400 text-white' : 'bg-brand/10 text-brand' }}">
                                {{ $loop->iteration }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-700">{{ $item->menu->name }}</p>
                            </div>
                            <span class="text-sm font-bold text-slate-800">{{ $item->total_qty }} <span class="text-xs font-normal text-slate-400">terjual</span></span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Aktivitas Terbaru</h3>
            @if ($recentPayments->isEmpty())
                <div class="flex h-40 flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-200">
                    <x-lucide-history class="h-7 w-7 text-slate-200" />
                    <p class="text-sm text-slate-400">Belum ada pembayaran.</p>
                </div>
            @else
                <ul class="space-y-4">
                    @foreach ($recentPayments as $payment)
                        <li class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                <x-lucide-check class="h-4 w-4" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-700">
                                    {{ $payment->order->order_number }} — {{ $payment->order->customer_name }}
                                </p>
                                <p class="text-xs text-slate-400">{{ $payment->order->table->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-slate-800">Rp{{ number_format($payment->amount_paid - $payment->change, 0, ',', '.') }}</p>
                                <p class="text-xs text-slate-400">{{ $payment->paid_at->format('H:i') }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('salesChart');
        if (!canvas || !window.Chart) return;

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: @js($chart['labels']),
                datasets: [{
                    label: 'Pendapatan',
                    data: @js($chart['data']),
                    backgroundColor: '#7C4A2D',   // brand
                    hoverBackgroundColor: '#A9714B', // brand-light
                    borderRadius: 6,
                    maxBarThickness: 48,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => 'Rp' + Number(ctx.parsed.y).toLocaleString('id-ID'),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Rp' + Number(value).toLocaleString('id-ID'),
                        },
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                    },
                    x: { grid: { display: false } },
                },
            },
        });
    });
</script>
@endpush
