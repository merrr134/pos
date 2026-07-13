@extends('layouts.app')

@section('title', 'Order Aktif')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6">
        <h2 class="font-display text-2xl font-bold">Order Aktif</h2>
        <p class="mt-1 text-sm text-white/80">Pantau pesanan yang sedang berjalan. Halaman ini hanya untuk monitoring — penyiapan pesanan diinformasikan manual oleh station.</p>
    </div>

    @if ($orders->isEmpty())
        <div class="rounded-xl border border-slate-100 bg-white p-10 text-center shadow-sm">
            <x-lucide-clipboard-list class="mx-auto h-10 w-10 text-slate-200" />
            <h3 class="mt-3 font-semibold text-slate-800">Tidak ada order aktif</h3>
            <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">
                Semua pesanan sudah dibayar. Order baru yang dibuat dari dashboard akan muncul di sini.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($orders as $order)
                @include('waiter.partials.order-card', ['order' => $order])
            @endforeach
        </div>

        {{-- Footer: info + pagination --}}
        <div class="mt-4 flex flex-col gap-3 rounded-xl border border-slate-100 bg-white px-5 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-400">
                Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} order aktif
            </p>
            <div>{{ $orders->onEachSide(1)->links() }}</div>
        </div>
    @endif
@endsection
