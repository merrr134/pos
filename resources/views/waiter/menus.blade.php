@extends('layouts.app')

@section('title', 'Menu (Stok)')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6">
        <h2 class="font-display text-2xl font-bold">Menu (Stok)</h2>
        <p class="mt-1 text-sm text-white/80">Tandai menu yang habis agar tidak bisa dipilih saat membuat order. Pengelolaan menu tetap dilakukan Admin.</p>
    </div>

    {{-- Kartu statistik --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand/10 text-brand">
                    <x-lucide-utensils-crossed class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Menu</p>
                    <p class="text-xl font-bold text-slate-800">{{ $stats['total'] }} Item</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                    <x-lucide-check-circle-2 class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Tersedia</p>
                    <p class="text-xl font-bold text-slate-800">{{ $stats['available'] }} Item</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                    <x-lucide-alert-circle class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Habis</p>
                    <p class="text-xl font-bold text-slate-800">{{ $stats['out'] }} Item</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Chip filter kategori --}}
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('waiter.menus', array_filter(['search' => $search])) }}"
           class="rounded-full px-4 py-1.5 text-sm font-medium {{ ! $category ? 'bg-brand text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand' }}">
            Semua
        </a>
        @foreach ($categories as $cat)
            <a href="{{ route('waiter.menus', array_filter(['search' => $search, 'category' => $cat->id])) }}"
               class="rounded-full px-4 py-1.5 text-sm font-medium {{ (string) $category === (string) $cat->id ? 'bg-brand text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    {{-- Kartu tabel --}}
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">

        {{-- Toolbar: search (GET) --}}
        <form method="GET" action="{{ route('waiter.menus') }}"
              class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            @if ($category)
                <input type="hidden" name="category" value="{{ $category }}" />
            @endif
            <div class="relative w-full sm:max-w-xs">
                <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama menu..."
                       class="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-brand focus:ring-brand" />
            </div>
            <button type="submit"
                    class="rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white hover:bg-brand-light">
                Cari
            </button>
        </form>

        {{-- Tabel: read-only kecuali toggle stok (FR-012) --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-medium">Foto</th>
                        <th class="px-5 py-3 font-medium">Nama Menu</th>
                        <th class="px-5 py-3 font-medium">Kategori</th>
                        <th class="px-5 py-3 font-medium">Harga</th>
                        <th class="px-5 py-3 font-medium">Stok/Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($menus as $menu)
                        <tr class="text-slate-700">
                            <td class="px-5 py-3">
                                @if ($menu->image)
                                    <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}"
                                         class="h-12 w-12 rounded-lg object-cover" />
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-300">
                                        <x-lucide-image class="h-5 w-5" />
                                    </div>
                                @endif
                            </td>

                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-800">{{ $menu->name }}</p>
                                @if ($menu->description)
                                    <p class="mt-0.5 max-w-[220px] truncate text-xs text-slate-400">{{ $menu->description }}</p>
                                @endif
                            </td>

                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-brand/10 px-2.5 py-0.5 text-xs font-semibold text-brand">
                                    {{ $menu->category->name }}
                                </span>
                            </td>

                            <td class="px-5 py-3 font-semibold text-slate-800">
                                Rp{{ number_format($menu->price, 0, ',', '.') }}
                            </td>

                            {{-- Toggle stok (hardcode per-state, purge-safe) --}}
                            <td class="px-5 py-3">
                                @if ($menu->is_available)
                                    <form action="{{ route('waiter.menus.availability', $menu) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Tandai habis"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full bg-brand transition-colors">
                                            <span class="inline-block h-4 w-4 translate-x-6 rounded-full bg-white transition-transform"></span>
                                        </button>
                                        <span class="text-xs font-medium text-brand">Tersedia</span>
                                    </form>
                                @else
                                    <form action="{{ route('waiter.menus.availability', $menu) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Tandai tersedia"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full bg-slate-300 transition-colors">
                                            <span class="inline-block h-4 w-4 translate-x-1 rounded-full bg-white transition-transform"></span>
                                        </button>
                                        <span class="text-xs font-medium text-rose-600">Habis</span>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">
                                Tidak ada menu yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: info + pagination --}}
        <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-400">
                Menampilkan {{ $menus->firstItem() ?? 0 }}–{{ $menus->lastItem() ?? 0 }} dari {{ $menus->total() }} menu
            </p>
            <div>{{ $menus->onEachSide(1)->links() }}</div>
        </div>
    </div>
@endsection
