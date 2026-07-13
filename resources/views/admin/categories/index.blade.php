@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    {{-- Hero header — WAJIB gradient brand (pakai utility .hero-header project) --}}
    <div class="hero-header mb-6">
        <h2 class="font-display text-2xl font-bold">Kategori</h2>
        <p class="mt-1 text-sm text-white/80">Kelola seluruh daftar menu, kategori, dan stok harian Anda dengan mudah.</p>
        <p class="mt-2 text-xs text-white/60">Menu Management <span class="mx-1">&rsaquo;</span> <span class="text-white/90">Kelola Kategori</span></p>
    </div>

    {{-- Ringkasan + tombol tambah (Figma) --}}
    <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm lg:col-span-2">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-semibold text-slate-800">Ringkasan Kategori</h3>
                    <p class="mt-1 max-w-md text-sm text-slate-500">
                        Kelola pengelompokan menu Anda untuk mempermudah barista dan pelanggan dalam memilih produk terbaik dari Pitou Cafe.
                    </p>
                </div>
                <div class="flex shrink-0 items-center gap-6">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-slate-800">{{ $stats['categories'] }}</p>
                        <p class="text-xs text-slate-400">Total<br>Kategori</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-slate-800">{{ $stats['menus'] }}</p>
                        <p class="text-xs text-slate-400">Total<br>Produk</p>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.categories.create') }}"
           class="flex items-center justify-center gap-3 rounded-xl bg-gradient-to-br from-brand to-brand-light p-5 text-white shadow-sm transition hover:opacity-95">
            <x-lucide-plus-circle class="h-7 w-7" />
            <span class="font-semibold">Tambah Kategori</span>
        </a>
    </div>

    {{-- Kartu tabel --}}
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">

        {{-- Toolbar: judul + search (GET) --}}
        <form method="GET" action="{{ route('admin.categories.index') }}"
              class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-sm font-semibold text-slate-700">Daftar Kategori Menu</h3>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kategori..."
                           class="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-brand focus:ring-brand sm:w-56" />
                </div>
                <button type="submit"
                        class="rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white hover:bg-brand-light">
                    Cari
                </button>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-medium">No</th>
                        <th class="px-5 py-3 font-medium">Nama Kategori</th>
                        <th class="px-5 py-3 font-medium">Jumlah Produk</th>
                        <th class="px-5 py-3 font-medium">Station</th>
                        <th class="px-5 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categories as $cat)
                        <tr class="text-slate-700">
                            <td class="px-5 py-3 text-slate-400">
                                {{ str_pad($categories->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand/10 text-brand">
                                        <x-lucide-tags class="h-4 w-4" />
                                    </span>
                                    <span class="font-medium text-slate-800">{{ $cat->name }}</span>
                                </div>
                            </td>

                            <td class="px-5 py-3 text-slate-500">{{ $cat->menus_count }} Produk</td>

                            <td class="px-5 py-3">
                                <x-station-badge :station="$cat->station" />
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.categories.edit', $cat) }}"
                                       class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-brand" title="Edit">
                                        <x-lucide-pencil class="h-4 w-4" />
                                    </a>

                                    @if ($cat->menus_count > 0)
                                        <span class="cursor-not-allowed rounded-lg p-2 text-slate-200"
                                              title="Kategori yang masih memiliki menu tidak bisa dihapus">
                                            <x-lucide-trash-2 class="h-4 w-4" />
                                        </span>
                                    @else
                                        <button type="button"
                                                @click="$store.deleteModal.trigger('{{ route('admin.categories.destroy', $cat) }}', @js($cat->name))"
                                                class="rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600" title="Hapus">
                                            <x-lucide-trash-2 class="h-4 w-4" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">
                                Belum ada kategori yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: info + pagination --}}
        <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-400">
                Menampilkan {{ $categories->firstItem() ?? 0 }}–{{ $categories->lastItem() ?? 0 }} dari {{ $categories->total() }} kategori
            </p>
            <div>{{ $categories->onEachSide(1)->links() }}</div>
        </div>
    </div>

    {{-- Info cards (Figma, konten disesuaikan sistem) --}}
    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-5">
            <div class="flex items-start gap-3">
                <div class="rounded-lg bg-amber-100 p-2 text-amber-700"><x-lucide-lightbulb class="h-5 w-5" /></div>
                <div>
                    <h4 class="font-semibold text-slate-800">Tips POS</h4>
                    <p class="mt-1 text-sm text-slate-500">
                        Gunakan nama kategori yang singkat untuk memudahkan navigasi pada layar sentuh saat Waiters membuat order.
                    </p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-5">
            <div class="flex items-start gap-3">
                <div class="rounded-lg bg-brand/10 p-2 text-brand"><x-lucide-split class="h-5 w-5" /></div>
                <div>
                    <h4 class="font-semibold text-slate-800">Routing Station</h4>
                    <p class="mt-1 text-sm text-slate-500">
                        Station pada kategori menentukan tujuan item pesanan: makanan diarahkan ke Kitchen, minuman ke Barista secara otomatis.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
