@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold">Laporan Penjualan</h2>
            <p class="mt-1 text-sm text-white/80">Analisis performa penjualan — seluruh angka dari transaksi terbayar (termasuk pajak).</p>
        </div>
        <a href="{{ route('admin.reports.export', array_filter(['date_from' => $from->toDateString(), 'date_to' => $to->toDateString()])) }}"
           target="_blank"
           class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-brand shadow-sm hover:bg-white/90">
            <x-lucide-download class="h-4 w-4" /> Unduh Laporan
        </a>
    </div>

    {{-- Filter rentang tanggal + preset --}}
    <div class="mb-6 rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.index') }}"
              class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div>
                    <label for="date_from" class="mb-1 block text-xs font-medium text-slate-500">Dari Tanggal</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $from->toDateString() }}"
                           class="rounded-lg border border-slate-200 py-2 px-3 text-sm focus:border-brand focus:ring-brand" />
                </div>
                <div>
                    <label for="date_to" class="mb-1 block text-xs font-medium text-slate-500">Sampai Tanggal</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $to->toDateString() }}"
                           class="rounded-lg border border-slate-200 py-2 px-3 text-sm focus:border-brand focus:ring-brand" />
                </div>
                <button type="submit"
                        class="rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white hover:bg-brand-light">
                    Terapkan
                </button>
            </div>

            {{-- Preset cepat (disetujui user) --}}
            @php($presets = [
                '7 Hari'    => ['date_from' => today()->subDays(6)->toDateString(), 'date_to' => today()->toDateString()],
                '30 Hari'   => ['date_from' => today()->subDays(29)->toDateString(), 'date_to' => today()->toDateString()],
                'Bulan Ini' => ['date_from' => today()->startOfMonth()->toDateString(), 'date_to' => today()->toDateString()],
            ])
            <div class="flex flex-wrap items-center gap-2">
                @foreach ($presets as $label => $params)
                    @php($isActive = $from->toDateString() === $params['date_from'] && $to->toDateString() === $params['date_to'])
                    <a href="{{ route('admin.reports.index', $params) }}"
                       class="rounded-full px-4 py-1.5 text-sm font-medium {{ $isActive ? 'bg-brand text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    {{-- 4 kartu ringkasan (Figma) --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand/10 text-brand">
                <x-lucide-wallet class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Total Pendapatan</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">Rp{{ number_format($stats->revenue, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                <x-lucide-receipt class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Total Pesanan</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $stats->orders }}</p>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 text-sky-600">
                <x-lucide-calculator class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Rata-rata Nilai Pesanan</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">Rp{{ number_format($average, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                <x-lucide-star class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Produk Terlaris</p>
            <p class="mt-1 truncate text-lg font-bold text-slate-800">{{ $topProduct?->menu->name ?? '—' }}</p>
        </div>
    </div>

    {{-- Tren Penjualan (peak GOLD) + Metode Pembayaran --}}
    <div class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-800">Tren Penjualan</h3>
                    <p class="text-xs text-slate-400">
                        {{ $from->format('d/m/Y') }} – {{ $to->format('d/m/Y') }} · batang tertinggi ditandai gold
                    </p>
                </div>
                <span class="h-3 w-3 rounded-sm bg-amber-500" title="Peak (gold)"></span>
            </div>

            @if (array_sum($chart['data']) > 0)
                <div class="h-64">
                    <canvas id="reportChart"></canvas>
                </div>
            @else
                <div class="flex h-40 flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-200">
                    <x-lucide-bar-chart-3 class="h-7 w-7 text-slate-200" />
                    <p class="text-sm text-slate-400">Tidak ada penjualan pada rentang ini.</p>
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <h3 class="mb-1 font-semibold text-slate-800">Metode Pembayaran</h3>
            <p class="mb-4 text-xs text-slate-400">Distribusi pendapatan per metode</p>

            @if ($methods['cash'] + $methods['qris'] > 0)
                <div class="space-y-5">
                    <div>
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700">Cash</span>
                            <span class="font-bold text-slate-800">{{ $methods['cash_percent'] }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ $methods['cash_percent'] }}%"></div>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Rp{{ number_format($methods['cash'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700">QRIS</span>
                            <span class="font-bold text-slate-800">{{ $methods['qris_percent'] }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-sky-500" style="width: {{ $methods['qris_percent'] }}%"></div>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Rp{{ number_format($methods['qris'], 0, ',', '.') }}</p>
                    </div>

                    <div class="flex items-center justify-between border-t border-dashed border-slate-200 pt-3 text-sm">
                        <span class="text-slate-500">Total Pajak Terkumpul</span>
                        <span class="font-bold text-slate-800">Rp{{ number_format($stats->tax, 0, ',', '.') }}</span>
                    </div>
                </div>
            @else
                <div class="flex h-40 flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-200">
                    <x-lucide-credit-card class="h-7 w-7 text-slate-200" />
                    <p class="text-sm text-slate-400">Belum ada pembayaran.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Produk Terlaris (paginate 5, tanpa kolom Trend — keputusan user) --}}
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="font-semibold text-slate-800">Produk Terlaris</h3>
            <p class="text-xs text-slate-400">Peringkat produk berdasarkan jumlah terjual pada rentang terpilih</p>
        </div>

        @if ($topProducts->isEmpty())
            <div class="px-5 py-12 text-center">
                <x-lucide-utensils-crossed class="mx-auto h-8 w-8 text-slate-200" />
                <p class="mt-2 text-sm text-slate-400">Belum ada produk terjual pada rentang ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">#</th>
                            <th class="px-5 py-3 font-medium">Nama Produk</th>
                            <th class="px-5 py-3 font-medium">Kategori</th>
                            <th class="px-5 py-3 text-right font-medium">Jumlah Terjual</th>
                            <th class="px-5 py-3 text-right font-medium">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($topProducts as $item)
                            @php($rank = ($topProducts->firstItem() ?? 1) + $loop->index)
                            <tr class="text-slate-700">
                                <td class="px-5 py-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold {{ $rank === 1 ? 'bg-amber-400 text-white' : 'bg-brand/10 text-brand' }}">
                                        {{ $rank }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-medium text-slate-800">{{ $item->menu->name }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center rounded-full bg-brand/10 px-2.5 py-0.5 text-xs font-semibold text-brand">
                                        {{ $item->menu->category->name }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right font-semibold">{{ $item->total_qty }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-800">Rp{{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-400">
                    Menampilkan {{ $topProducts->firstItem() ?? 0 }}–{{ $topProducts->lastItem() ?? 0 }} dari {{ $topProducts->total() }} produk
                </p>
                <div>{{ $topProducts->onEachSide(1)->links() }}</div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('reportChart');
        if (!canvas || !window.Chart) return;

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: @js($chart['labels']),
                datasets: [{
                    label: 'Pendapatan',
                    data: @js($chart['data']),
                    // Warna dihitung server: peak = gold (SRS FR-011), sisanya brand.
                    backgroundColor: @js($chart['colors']),
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
                        ticks: { callback: (value) => 'Rp' + Number(value).toLocaleString('id-ID') },
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                    },
                    x: { grid: { display: false } },
                },
            },
        });
    });
</script>
@endpush
