@extends('layouts.app')

@section('title', 'Edit Menu')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6">
        <a href="{{ route('admin.menus.index') }}"
           class="mb-3 inline-flex items-center gap-1.5 text-sm text-white/70 hover:text-white">
            <x-lucide-arrow-left class="h-4 w-4" /> Kembali ke Menu Management
        </a>
        <h2 class="font-display text-2xl font-bold">Edit Menu</h2>
        <p class="mt-1 text-sm text-white/80">Perbarui detail menu "{{ $menu->name }}".</p>
    </div>

    <div class="mx-auto max-w-2xl rounded-xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
        @include('admin.menus.partials.form')
    </div>
@endsection
