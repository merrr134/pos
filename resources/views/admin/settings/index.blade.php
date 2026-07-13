@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6">
        <h2 class="font-display text-2xl font-bold">Pengaturan Sistem</h2>
        <p class="mt-1 text-sm text-white/80">Kelola preferensi kedai Anda. Pengaturan berlaku otomatis ke seluruh sistem.</p>
    </div>

    <div class="mx-auto max-w-2xl space-y-6">
        {{-- Kartu Pengaturan Pembayaran (Figma pengaturan.png — kartu lain menyusul di Modul Settings) --}}
        <div class="rounded-xl border border-slate-100 bg-white shadow-sm">
            <div class="flex items-center gap-3 border-b border-slate-100 p-5">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand/10 text-brand">
                    <x-lucide-percent class="h-5 w-5" />
                </span>
                <div>
                    <h3 class="font-semibold text-slate-800">Pengaturan Pembayaran</h3>
                    <p class="text-xs text-slate-400">Pajak dihitung otomatis di kasir dari nilai ini.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5 p-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="tax_percent" class="mb-1.5 block text-sm font-medium text-slate-700">Pajak (%)</label>
                    <div class="relative max-w-xs">
                        <input type="number" id="tax_percent" name="tax_percent" min="0" max="100" step="0.5"
                               value="{{ old('tax_percent', rtrim(rtrim(number_format($taxPercent, 2, '.', ''), '0'), '.')) }}"
                               class="w-full rounded-lg border border-slate-200 py-2.5 pl-3 pr-10 text-sm font-semibold focus:border-brand focus:ring-brand @error('tax_percent') border-red-300 @enderror" />
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">%</span>
                    </div>
                    @error('tax_percent')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-400">
                        Isi 0 untuk menonaktifkan pajak. Perubahan hanya berlaku untuk pembayaran BARU —
                        struk & transaksi lama tetap memakai pajak saat itu (snapshot).
                    </p>
                </div>

                <div class="flex items-center justify-end border-t border-slate-100 pt-5">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-light">
                        <x-lucide-check class="h-4 w-4" /> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Placeholder setting lain (Modul Settings penuh menyusul) --}}
        <div class="rounded-xl border border-dashed border-slate-200 bg-white/60 p-5 text-center">
            <p class="text-sm text-slate-400">
                Pengaturan lain (profil toko, jam operasional, preferensi sistem) akan tersedia di Modul Settings.
            </p>
        </div>
    </div>
@endsection
