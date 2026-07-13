@extends('layouts.app')

@section('title', 'Kelola Meja')

@section('content')
    {{-- Hero header — WAJIB gradient brand (pakai utility .hero-header project) --}}
    <div class="hero-header mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold">Kelola Meja</h2>
            <p class="mt-1 text-sm text-white/80">Atur tata letak dan ketersediaan meja restoran Anda.</p>
        </div>
        <a href="{{ route('admin.tables.create') }}"
           class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-brand shadow-sm hover:bg-white/90">
            <x-lucide-plus class="h-4 w-4" /> Tambah Meja Baru
        </a>
    </div>

    {{-- Kartu statistik (Figma: Total Meja / Meja Terisi / Meja Kosong) --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand/10 text-brand">
                    <x-lucide-armchair class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Meja</p>
                    <p class="text-xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                    <x-lucide-users class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Meja Terisi</p>
                    <p class="text-xl font-bold text-slate-800">{{ str_pad($stats['terisi'], 2, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                    <x-lucide-check-circle-2 class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Meja Kosong</p>
                    <p class="text-xl font-bold text-slate-800">{{ str_pad($stats['kosong'], 2, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Chip filter status + search --}}
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.tables.index', array_filter(['search' => $search])) }}"
           class="rounded-full px-4 py-1.5 text-sm font-medium {{ ! in_array($status, ['kosong', 'terisi'], true) ? 'bg-brand text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand' }}">
            Semua
        </a>
        <a href="{{ route('admin.tables.index', array_filter(['search' => $search, 'status' => 'kosong'])) }}"
           class="rounded-full px-4 py-1.5 text-sm font-medium {{ $status === 'kosong' ? 'bg-brand text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand' }}">
            Kosong
        </a>
        <a href="{{ route('admin.tables.index', array_filter(['search' => $search, 'status' => 'terisi'])) }}"
           class="rounded-full px-4 py-1.5 text-sm font-medium {{ $status === 'terisi' ? 'bg-brand text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand' }}">
            Terisi
        </a>

        <form method="GET" action="{{ route('admin.tables.index') }}" class="ml-auto flex items-center gap-2">
            @if (in_array($status, ['kosong', 'terisi'], true))
                <input type="hidden" name="status" value="{{ $status }}" />
            @endif
            <div class="relative">
                <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama meja..."
                       class="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-brand focus:ring-brand sm:w-52" />
            </div>
            <button type="submit"
                    class="rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white hover:bg-brand-light">
                Cari
            </button>
        </form>
    </div>

    {{-- Grid kartu meja (Figma) --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse ($tables as $table)
            <div class="rounded-xl border bg-white p-4 shadow-sm {{ $table->status === 'terisi' ? 'border-brand/30' : 'border-slate-100' }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center rounded-lg bg-brand px-2.5 py-1 text-xs font-semibold text-white">
                            {{ $table->name }}
                        </span>
                        @if ($table->is_vip)
                            <span class="inline-flex items-center gap-1 rounded-lg bg-amber-400 px-2 py-1 text-xs font-bold text-white" title="Meja VIP — diprioritaskan di antrian station">
                                <x-lucide-crown class="h-3 w-3" /> VIP
                            </span>
                        @endif
                    </div>
                    <x-table-status-badge :status="$table->status" />
                </div>

                <div class="my-5 flex items-center justify-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full {{ $table->status === 'terisi' ? 'bg-brand/10 text-brand' : 'bg-emerald-50 text-emerald-500' }}">
                        <x-lucide-armchair class="h-7 w-7" />
                    </div>
                </div>

                <div class="flex items-center justify-center gap-1 border-t border-slate-100 pt-3">
                    <a href="{{ route('admin.tables.edit', $table) }}"
                       class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-brand" title="Edit">
                        <x-lucide-pencil class="h-4 w-4" />
                    </a>

                    @if ($table->status === 'terisi' || $table->orders_exists)
                        <span class="cursor-not-allowed rounded-lg p-2 text-slate-200"
                              title="{{ $table->status === 'terisi' ? 'Meja sedang terisi, tidak bisa dihapus' : 'Meja dengan riwayat order tidak bisa dihapus' }}">
                            <x-lucide-trash-2 class="h-4 w-4" />
                        </span>
                    @else
                        <button type="button"
                                @click="$store.deleteModal.trigger('{{ route('admin.tables.destroy', $table) }}', @js($table->name))"
                                class="rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600" title="Hapus">
                            <x-lucide-trash-2 class="h-4 w-4" />
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-slate-100 bg-white px-5 py-10 text-center text-sm text-slate-400">
                Tidak ada meja yang cocok dengan pencarian.
            </div>
        @endforelse

        {{-- Kartu "Tambah Meja" (Figma) --}}
        <a href="{{ route('admin.tables.create') }}"
           class="flex min-h-[170px] flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 bg-white/60 text-slate-400 transition hover:border-brand hover:text-brand">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100">
                <x-lucide-plus class="h-5 w-5" />
            </span>
            <span class="text-sm font-medium">Tambah Meja</span>
        </a>
    </div>

    {{-- Footer: info + pagination --}}
    <div class="mt-4 flex flex-col gap-3 rounded-xl border border-slate-100 bg-white px-5 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs text-slate-400">
            Menampilkan {{ $tables->firstItem() ?? 0 }}–{{ $tables->lastItem() ?? 0 }} dari {{ $tables->total() }} meja
        </p>
        <div>{{ $tables->onEachSide(1)->links() }}</div>
    </div>
@endsection
