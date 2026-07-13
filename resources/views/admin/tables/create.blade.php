@extends('layouts.app')

@section('title', 'Tambah Meja')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6">
        <a href="{{ route('admin.tables.index') }}"
           class="mb-3 inline-flex items-center gap-1.5 text-sm text-white/70 hover:text-white">
            <x-lucide-arrow-left class="h-4 w-4" /> Kembali ke Kelola Meja
        </a>
        <h2 class="font-display text-2xl font-bold">Tambah Meja Baru</h2>
        <p class="mt-1 text-sm text-white/80">Meja baru otomatis berstatus kosong dan siap menerima order.</p>
    </div>

    <div class="mx-auto max-w-2xl rounded-xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
        @include('admin.tables.partials.form', ['table' => null])
    </div>
@endsection
