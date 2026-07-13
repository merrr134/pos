@extends('layouts.app')

@section('title', 'Menu Management')

@section('content')
    {{-- Hero header — WAJIB gradient brand (pakai utility .hero-header project) --}}
    <div class="hero-header mb-6">
        <h2 class="font-display text-2xl font-bold">Menu Management</h2>
        <p class="mt-1 text-sm text-white/80">Kelola seluruh daftar menu, kategori, dan stok harian Anda dengan mudah.</p>
    </div>

    {{-- Kartu statistik (Figma: Total Menu / Tersedia / Stok Habis) --}}
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
                    <p class="text-sm text-slate-500">Stok Habis</p>
                    <p class="text-xl font-bold text-slate-800">{{ $stats['out'] }} Item</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Chip filter kategori + tombol tambah (Figma) --}}
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.menus.index', array_filter(['search' => $search])) }}"
           class="rounded-full px-4 py-1.5 text-sm font-medium {{ ! $category ? 'bg-brand text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand' }}">
            Semua
        </a>
        @foreach ($categories as $cat)
            <a href="{{ route('admin.menus.index', array_filter(['search' => $search, 'category' => $cat->id])) }}"
               class="rounded-full px-4 py-1.5 text-sm font-medium {{ (string) $category === (string) $cat->id ? 'bg-brand text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand' }}">
                {{ $cat->name }}
            </a>
        @endforeach

        <a href="{{ route('admin.menus.create') }}"
           class="ml-auto inline-flex items-center gap-2 whitespace-nowrap rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-light">
            <x-lucide-plus class="h-4 w-4" /> Tambah Menu Baru
        </a>
    </div>

    {{-- Kartu tabel --}}
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">

        {{-- Toolbar: search (GET) --}}
        <form method="GET" action="{{ route('admin.menus.index') }}"
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

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-medium">Foto</th>
                        <th class="px-5 py-3 font-medium">Nama Menu</th>
                        <th class="px-5 py-3 font-medium">Kategori</th>
                        <th class="px-5 py-3 font-medium">Harga</th>
                        <th class="px-5 py-3 font-medium">Stok/Status</th>
                        <th class="px-5 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($menus as $menu)
                        <tr class="text-slate-700">
                            {{-- Foto --}}
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

                            {{-- Nama + deskripsi --}}
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-800">{{ $menu->name }}</p>
                                @if ($menu->description)
                                    <p class="mt-0.5 max-w-[220px] truncate text-xs text-slate-400">{{ $menu->description }}</p>
                                @endif
                            </td>

                            {{-- Kategori --}}
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-brand/10 px-2.5 py-0.5 text-xs font-semibold text-brand">
                                    {{ $menu->category->name }}
                                </span>
                            </td>

                            {{-- Harga --}}
                            <td class="px-5 py-3 font-semibold text-slate-800">
                                Rp{{ number_format($menu->price, 0, ',', '.') }}
                            </td>

                            {{-- Stok/Status: toggle (hardcode per-state, purge-safe) --}}
                            <td class="px-5 py-3">
                                @if ($menu->is_available)
                                    <form action="{{ route('admin.menus.toggle-availability', $menu) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Tandai habis"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full bg-brand transition-colors">
                                            <span class="inline-block h-4 w-4 translate-x-6 rounded-full bg-white transition-transform"></span>
                                        </button>
                                        <span class="text-xs font-medium text-brand">Tersedia</span>
                                    </form>
                                @else
                                    <form action="{{ route('admin.menus.toggle-availability', $menu) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Tandai tersedia"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full bg-slate-300 transition-colors">
                                            <span class="inline-block h-4 w-4 translate-x-1 rounded-full bg-white transition-transform"></span>
                                        </button>
                                        <span class="text-xs font-medium text-rose-600">Habis</span>
                                    </form>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.menus.edit', $menu) }}"
                                       class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-brand" title="Edit">
                                        <x-lucide-pencil class="h-4 w-4" />
                                    </a>

                                    @if ($menu->order_items_exists)
                                        <span class="cursor-not-allowed rounded-lg p-2 text-slate-200"
                                              title="Menu dengan riwayat order tidak bisa dihapus">
                                            <x-lucide-trash-2 class="h-4 w-4" />
                                        </span>
                                    @else
                                        <button type="button"
                                                @click="$store.deleteModal.trigger('{{ route('admin.menus.destroy', $menu) }}', @js($menu->name))"
                                                class="rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600" title="Hapus">
                                            <x-lucide-trash-2 class="h-4 w-4" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">
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
