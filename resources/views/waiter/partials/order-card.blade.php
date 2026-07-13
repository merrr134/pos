{{--
    Kartu order aktif (FR-013) — murni READ-ONLY.
    BR-009/BR-010: tidak ada status masak (badge Figma MENUNGGU/DIMASAK/SELESAI
    sengaja diganti badge netral "AKTIF"), tidak ada tombol aksi apa pun.
    Dipakai di: strip dashboard waiter + halaman waiter/orders.
--}}
@php
    $foodQty  = $order->items->where('station', 'kitchen')->sum('qty');
    $drinkQty = $order->items->where('station', 'barista')->sum('qty');
    $summary  = collect([
        $foodQty ? "{$foodQty} Makanan" : null,
        $drinkQty ? "{$drinkQty} Minuman" : null,
    ])->filter()->join(', ');
@endphp

<div class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
    <div class="flex items-center justify-between">
        <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $order->order_number }}</span>
        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-700">Aktif</span>
    </div>

    <p class="mt-2 font-semibold text-slate-800">{{ $order->table->name }} — {{ $order->customer_name }}</p>

    <div class="mt-2 flex items-center justify-between text-xs text-slate-400">
        <span>{{ $summary ?: '—' }}</span>
        <span>{{ $order->created_at->diffForHumans(short: true) }}</span>
    </div>
</div>
