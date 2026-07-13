@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6">
        <a href="{{ route('admin.categories.index') }}"
           class="mb-3 inline-flex items-center gap-1.5 text-sm text-white/70 hover:text-white">
            <x-lucide-arrow-left class="h-4 w-4" /> Kembali ke Kategori
        </a>
        <h2 class="font-display text-2xl font-bold">Tambah Kategori Baru</h2>
        <p class="mt-1 text-sm text-white/80">Buat kategori menu beserta station tujuan penyiapannya.</p>
    </div>

    <div class="mx-auto max-w-2xl rounded-xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
        @include('admin.categories.partials.form', ['category' => null])
    </div>
@endsection
