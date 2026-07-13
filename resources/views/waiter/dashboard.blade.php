@extends('layouts.app')

@section('title', 'Waiter')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6">
        <h2 class="font-display text-2xl font-bold">Dashboard Waiter</h2>
        <p class="mt-1 text-sm text-white/80">Pilih meja kosong, susun pesanan pelanggan, lalu kirim ke station.</p>
    </div>

    {{-- Ringkasan error validasi (server-side) --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
            <div class="flex items-start gap-3">
                <x-lucide-alert-circle class="mt-0.5 h-5 w-5 shrink-0 text-red-500" />
                <div class="text-sm text-red-700">
                    <p class="font-semibold">Order belum bisa dikirim:</p>
                    <ul class="mt-1 list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{--
        Satu form untuk seluruh halaman (Alpine + form submit biasa, tanpa fetch).
        Semua tombol interaktif di dalamnya WAJIB type="button" agar tidak men-submit form.
    --}}
    <form method="POST" action="{{ route('orders.store') }}" x-data="orderPage()"
          @submit="if (!canSubmit) $event.preventDefault()">
        @csrf
        <input type="hidden" name="table_id" :value="selectedTable ? selectedTable.id : ''" />
        {{-- Hidden inputs cart — dirender ulang oleh Alpine setiap cart berubah --}}
        <template x-for="(item, i) in cart" :key="'h' + item.id">
            <span hidden>
                <input type="hidden" :name="`items[${i}][menu_id]`" :value="item.id" />
                <input type="hidden" :name="`items[${i}][qty]`" :value="item.qty" />
            </span>
        </template>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- ================= KOLOM KIRI ================= --}}
            <div class="space-y-6 xl:col-span-2">

                {{-- Manajemen Meja --}}
                <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-semibold text-slate-800">Manajemen Meja</h3>
                        <div class="flex items-center gap-4 text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Kosong
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-rose-500"></span> Terisi
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-6">
                        @foreach ($tables as $table)
                            @if ($table->status === 'kosong')
                                <button type="button"
                                        @click="selectTable({{ $table->id }}, @js($table->name))"
                                        :class="selectedTable && selectedTable.id === {{ $table->id }}
                                            ? 'border-brand ring-2 ring-brand/30'
                                            : 'border-slate-200 hover:border-brand/50'"
                                        class="flex flex-col items-center gap-1.5 rounded-xl border bg-white p-3 transition">
                                    <span class="text-sm font-bold text-slate-800">{{ $table->name }}</span>
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-700">Kosong</span>
                                </button>
                            @else
                                <div class="flex cursor-not-allowed flex-col items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50/50 p-3 opacity-70"
                                     title="{{ $table->name }} sedang terisi">
                                    <span class="text-sm font-bold text-slate-400">{{ $table->name }}</span>
                                    <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-rose-600">Terisi</span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @if ($tables->where('status', 'kosong')->isEmpty())
                        <p class="mt-4 text-center text-sm text-slate-400">Semua meja sedang terisi. Tunggu sampai ada meja kosong.</p>
                    @endif
                </div>

                {{-- Nama Pelanggan (WAJIB — SRS FR-005; Figma menulis "opsional" tapi SRS menang) --}}
                <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                    <label for="customer_name" class="mb-2 block font-semibold text-slate-800">
                        Nama Pelanggan <span class="text-xs font-normal text-red-500">*wajib</span>
                    </label>
                    <input type="text" id="customer_name" name="customer_name" x-model="customer"
                           maxlength="100" placeholder="Masukkan nama pelanggan..."
                           class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-brand focus:ring-brand @error('customer_name') border-red-300 @enderror" />
                    @error('customer_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Daftar Menu --}}
                <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="font-semibold text-slate-800">Pilih Menu</h3>
                        <div class="relative">
                            <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <input type="text" x-model="search" placeholder="Cari menu..."
                                   class="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-brand focus:ring-brand sm:w-56" />
                        </div>
                    </div>

                    {{-- Chip kategori --}}
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <button type="button" @click="category = null"
                                :class="category === null ? 'bg-brand text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand'"
                                class="rounded-full px-4 py-1.5 text-sm font-medium">
                            Semua
                        </button>
                        @foreach ($categories as $cat)
                            <button type="button" @click="category = {{ $cat->id }}"
                                    :class="category === {{ $cat->id }} ? 'bg-brand text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand'"
                                    class="rounded-full px-4 py-1.5 text-sm font-medium">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Grid kartu menu (client-side: filter kategori + search) --}}
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                        <template x-for="menu in filteredMenus" :key="menu.id">
                            <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                                <div class="relative h-24 bg-slate-100">
                                    <template x-if="menu.image">
                                        <img :src="menu.image" :alt="menu.name" class="h-full w-full object-cover" />
                                    </template>
                                    <template x-if="!menu.image">
                                        <div class="flex h-full w-full items-center justify-center text-slate-300">
                                            <x-lucide-image class="h-7 w-7" />
                                        </div>
                                    </template>
                                    <button type="button" @click="add(menu)" :title="'Tambah ' + menu.name"
                                            class="absolute bottom-2 right-2 flex h-8 w-8 items-center justify-center rounded-full bg-brand text-white shadow hover:bg-brand-light">
                                        <x-lucide-plus class="h-4 w-4" />
                                    </button>
                                </div>
                                <div class="p-3">
                                    <p class="truncate text-sm font-semibold text-slate-800" x-text="menu.name"></p>
                                    <p class="mt-0.5 text-xs font-medium text-brand" x-text="rupiah(menu.price)"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <p x-show="filteredMenus.length === 0" x-cloak class="py-8 text-center text-sm text-slate-400">
                        Tidak ada menu yang cocok dengan pencarian.
                    </p>
                </div>
            </div>

            {{-- ================= PANEL CART (kanan) ================= --}}
            <div>
                <div class="sticky top-6 overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                    {{-- Header cart --}}
                    <div class="border-b border-slate-100 bg-cream p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800"
                                x-text="selectedTable ? 'Order ' + selectedTable.name : 'Order Baru'"></h3>
                            <span class="inline-flex items-center rounded-full bg-brand/10 px-2.5 py-0.5 text-[11px] font-semibold text-brand">Baru</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                            <span>Pelanggan:</span>
                            <span class="font-semibold text-slate-700" x-text="customer.trim() || '—'"></span>
                        </div>
                        <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                            <span>Meja:</span>
                            <span class="font-semibold text-slate-700" x-text="selectedTable ? selectedTable.name : 'Belum dipilih'"></span>
                        </div>
                    </div>

                    {{-- Item cart --}}
                    <div class="max-h-80 overflow-y-auto p-4">
                        <template x-if="cart.length === 0">
                            <div class="py-8 text-center">
                                <x-lucide-shopping-cart class="mx-auto h-8 w-8 text-slate-200" />
                                <p class="mt-2 text-sm text-slate-400">Belum ada item. Tambahkan menu dari daftar.</p>
                            </div>
                        </template>

                        <div class="space-y-3">
                            <template x-for="item in cart" :key="item.id">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                        <template x-if="item.image">
                                            <img :src="item.image" :alt="item.name" class="h-full w-full object-cover" />
                                        </template>
                                        <template x-if="!item.image">
                                            <div class="flex h-full w-full items-center justify-center text-slate-300">
                                                <x-lucide-image class="h-4 w-4" />
                                            </div>
                                        </template>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-slate-800" x-text="item.name"></p>
                                        <p class="text-xs text-slate-400" x-text="rupiah(item.price)"></p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="dec(item)" title="Kurangi"
                                                class="flex h-6 w-6 items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:border-brand hover:text-brand">
                                            <x-lucide-minus class="h-3 w-3" />
                                        </button>
                                        <span class="w-6 text-center text-sm font-semibold text-slate-800" x-text="item.qty"></span>
                                        <button type="button" @click="inc(item)" title="Tambah"
                                                class="flex h-6 w-6 items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:border-brand hover:text-brand">
                                            <x-lucide-plus class="h-3 w-3" />
                                        </button>
                                        <button type="button" @click="remove(item)" title="Hapus item"
                                                class="ml-1 flex h-6 w-6 items-center justify-center rounded-full text-slate-300 hover:bg-red-50 hover:text-red-500">
                                            <x-lucide-trash-2 class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Total + submit --}}
                    <div class="border-t border-slate-100 p-4">
                        <div class="flex items-center justify-between text-sm text-slate-500">
                            <span>Subtotal</span>
                            <span x-text="rupiah(subtotal)"></span>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="font-semibold text-slate-800">Total</span>
                            <span class="text-lg font-bold text-brand" x-text="rupiah(subtotal)"></span>
                        </div>

                        <button type="submit" :disabled="!canSubmit"
                                class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-light disabled:cursor-not-allowed disabled:opacity-40">
                            <x-lucide-send class="h-4 w-4" /> Kirim Pesanan
                        </button>

                        <p x-show="!canSubmit" x-cloak class="mt-2 text-center text-[11px] text-slate-400"
                           x-text="submitHint"></p>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Strip Pesanan Aktif (FR-013, read-only) — di luar form order agar tidak mengganggu submit --}}
    <div class="mt-6 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-lucide-clipboard-list class="h-4 w-4 text-slate-400" />
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Pesanan Aktif</h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400">{{ $activeOrdersTotal }} pesanan sedang diproses</span>
                @if ($activeOrdersTotal > $activeOrders->count())
                    <a href="{{ route('waiter.orders') }}" class="text-xs font-semibold text-brand hover:text-brand-light">
                        Lihat semua →
                    </a>
                @endif
            </div>
        </div>

        @if ($activeOrders->isEmpty())
            <p class="py-4 text-center text-sm text-slate-400">Belum ada pesanan aktif. Order baru akan muncul di sini.</p>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($activeOrders as $order)
                    @include('waiter.partials.order-card', ['order' => $order])
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('orderPage', () => ({
            menus: @js($menuData),
            selectedTable: null,          // { id, name }
            customer: @js(old('customer_name', '')),
            category: null,               // null = semua
            search: '',
            cart: [],                     // { id, name, price, image, qty }

            init() {
                // Pulihkan pilihan saat validasi server gagal (old input).
                const oldTableId = Number(@js(old('table_id'))) || null;
                const tableNames = @js($tables->pluck('name', 'id'));
                if (oldTableId && tableNames[oldTableId]) {
                    this.selectedTable = { id: oldTableId, name: tableNames[oldTableId] };
                }

                for (const row of @js(old('items', []))) {
                    const menu = this.menus.find(m => m.id === Number(row.menu_id));
                    if (menu) {
                        this.cart.push({ ...menu, qty: Math.max(1, parseInt(row.qty, 10) || 1) });
                    }
                }
            },

            get filteredMenus() {
                const q = this.search.trim().toLowerCase();
                return this.menus.filter(m =>
                    (this.category === null || m.category_id === this.category)
                    && (q === '' || m.name.toLowerCase().includes(q))
                );
            },

            selectTable(id, name) {
                this.selectedTable = { id, name };
            },

            add(menu) {
                const existing = this.cart.find(i => i.id === menu.id);
                existing ? existing.qty++ : this.cart.push({ ...menu, qty: 1 });
            },
            inc(item) { item.qty++; },
            dec(item) { item.qty > 1 ? item.qty-- : this.remove(item); },
            remove(item) { this.cart = this.cart.filter(i => i.id !== item.id); },

            get subtotal() {
                return this.cart.reduce((sum, i) => sum + i.price * i.qty, 0);
            },

            get canSubmit() {
                return this.selectedTable !== null && this.customer.trim() !== '' && this.cart.length > 0;
            },

            get submitHint() {
                if (!this.selectedTable) return 'Pilih meja kosong terlebih dahulu.';
                if (!this.customer.trim()) return 'Isi nama pelanggan terlebih dahulu.';
                if (!this.cart.length) return 'Tambahkan minimal 1 menu.';
                return '';
            },

            rupiah(n) {
                return 'Rp' + Number(n).toLocaleString('id-ID');
            },
        }));
    });
</script>
@endpush
