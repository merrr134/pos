{{--
    Fragment antrian Kitchen — dirender server-side dan dipakai dua arah:
    1. Include awal di kitchen/index.blade.php (di dalam #kitchen-queue).
    2. Dikembalikan endpoint queue-status (JSON.html) untuk partial refresh —
       hanya container #kitchen-queue yang di-swap; header/jam/toggle tidak tersentuh.
    Variabel: $unprinted (VIP dulu → FIFO, BR-015), $printed (FIFO).
--}}

{{-- Banner order teratas kelompok Belum Dicetak (VIP otomatis di depan, BR-015) --}}
@php($next = $unprinted->first())
@if ($next)
    @if ($next->table->is_vip)
        {{-- Varian VIP — lebih mencolok (hardcode terpisah, purge-safe) --}}
        <div class="mb-6 flex flex-col gap-3 rounded-xl border-2 border-amber-400 bg-amber-100 p-4 shadow-md ring-2 ring-amber-200 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 animate-pulse items-center justify-center rounded-full bg-amber-400 text-white">
                    <x-lucide-crown class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-sm font-bold text-amber-900">
                        Pesanan VIP Menunggu
                        <span class="ml-1 inline-flex items-center rounded-full bg-amber-400 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">VIP</span>
                    </p>
                    <p class="text-xs text-amber-800">
                        {{ $next->table->name }} memesan {{ $next->items->sum('qty') }} item makanan — prioritaskan order ini.
                    </p>
                </div>
            </div>
            <a href="#order-{{ $next->id }}"
               class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg bg-amber-500 px-4 py-2 text-sm font-bold text-white hover:bg-amber-600">
                Lihat Pesanan
            </a>
        </div>
    @else
        <div class="mb-6 flex flex-col gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                    <x-lucide-alert-circle class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Pesanan Berikutnya</p>
                    <p class="text-xs text-slate-500">
                        {{ $next->table->name }} memesan {{ $next->items->sum('qty') }} item makanan.
                    </p>
                </div>
            </div>
            <a href="#order-{{ $next->id }}"
               class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-light">
                Lihat Pesanan
            </a>
        </div>
    @endif
@endif

{{-- ===== Kelompok 1: Belum Dicetak (VIP dulu → FIFO) ===== --}}
<div class="mb-3 flex items-center gap-2">
    <x-lucide-printer class="h-4 w-4 text-slate-400" />
    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Belum Dicetak</h3>
    <span class="rounded-full bg-brand/10 px-2 py-0.5 text-xs font-semibold text-brand">{{ $unprinted->count() }}</span>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
    @foreach ($unprinted as $order)
        @php($isVip = $order->table->is_vip)
        <div id="order-{{ $order->id }}"
             class="flex flex-col rounded-xl bg-white shadow-sm {{ $isVip ? 'border-2 border-amber-400 ring-2 ring-amber-100' : 'border border-slate-100' }}">
            <div class="flex items-start justify-between p-4 pb-0">
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center rounded-md bg-brand/10 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-brand">
                        {{ $order->order_number }}
                    </span>
                    @if ($isVip)
                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-400 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-white">
                            <x-lucide-crown class="h-3 w-3" /> VIP
                        </span>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold tabular-nums text-slate-800">{{ $order->created_at->format('H:i') }}</p>
                    <p class="text-[11px] text-slate-400">Lama Tunggu: {{ $order->created_at->diffInMinutes(now()) }}m</p>
                </div>
            </div>

            <div class="px-4 pt-1">
                <h3 class="font-display text-xl font-bold text-slate-800">{{ $order->table->name }}</h3>
                <p class="text-xs text-slate-500">Customer: {{ $order->customer_name }}</p>
            </div>

            <div class="flex-1 space-y-2 p-4">
                @foreach ($order->items as $item)
                    <div class="flex items-center gap-2 text-sm">
                        <span class="w-8 shrink-0 font-bold text-brand">{{ $item->qty }}x</span>
                        <span class="text-slate-700">{{ $item->menu->name }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Satu-satunya aksi station: cetak checker — hanya bisa SEKALI (BR-014) --}}
            <div class="border-t border-slate-100 p-3">
                <a href="{{ route('kitchen.checker', $order) }}" target="_blank"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:border-brand hover:text-brand">
                    <x-lucide-printer class="h-4 w-4" /> Cetak Checker
                </a>
            </div>
        </div>
    @endforeach

    {{-- Kartu "Menunggu Pesanan" (Figma) --}}
    <div class="flex min-h-[220px] flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-200 bg-white/60 p-6 text-center">
        <span class="flex h-12 w-12 animate-pulse items-center justify-center rounded-full bg-rose-50 text-rose-300">
            <x-lucide-hourglass class="h-6 w-6" />
        </span>
        <div>
            <p class="text-sm font-semibold text-slate-400">Menunggu Pesanan</p>
            <p class="mt-1 text-xs text-slate-300">Layar akan otomatis diperbarui saat ada pesanan masuk.</p>
        </div>
    </div>
</div>

{{-- ===== Kelompok 2: Sudah Dicetak (order TIDAK hilang, pindah ke bawah) ===== --}}
@if ($printed->isNotEmpty())
    <div class="mb-3 mt-8 flex items-center gap-2">
        <x-lucide-check-circle-2 class="h-4 w-4 text-emerald-500" />
        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Sudah Dicetak</h3>
        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">{{ $printed->count() }}</span>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($printed as $order)
            @php($isVip = $order->table->is_vip)
            <div class="flex flex-col rounded-xl bg-white opacity-70 shadow-sm {{ $isVip ? 'border-2 border-amber-300' : 'border border-slate-100' }}">
                <div class="flex items-start justify-between p-4 pb-0">
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                            {{ $order->order_number }}
                        </span>
                        @if ($isVip)
                            <span class="inline-flex items-center gap-1 rounded-md bg-amber-300 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-white">
                                <x-lucide-crown class="h-3 w-3" /> VIP
                            </span>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold tabular-nums text-slate-500">{{ $order->created_at->format('H:i') }}</p>
                        <p class="text-[11px] text-slate-400">Lama Tunggu: {{ $order->created_at->diffInMinutes(now()) }}m</p>
                    </div>
                </div>

                <div class="px-4 pt-1">
                    <h3 class="font-display text-xl font-bold text-slate-500">{{ $order->table->name }}</h3>
                    <p class="text-xs text-slate-400">Customer: {{ $order->customer_name }}</p>
                </div>

                <div class="flex-1 space-y-2 p-4">
                    @foreach ($order->items as $item)
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-8 shrink-0 font-bold text-slate-400">{{ $item->qty }}x</span>
                            <span class="text-slate-500">{{ $item->menu->name }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- BR-014: sudah dicetak — tombol nonaktif, tidak bisa cetak ulang --}}
                <div class="border-t border-slate-100 p-3">
                    <span class="inline-flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-400"
                          title="Checker hanya bisa dicetak satu kali">
                        <x-lucide-check class="h-4 w-4" /> Sudah Dicetak
                    </span>
                </div>
            </div>
        @endforeach
    </div>
@endif
