@php
    $table  = $table ?? null;
    $isEdit = (bool) $table;
    $action = $isEdit ? route('admin.tables.update', $table) : route('admin.tables.store');
@endphp

<form action="{{ $action }}" method="POST" class="space-y-5"
      x-data="{ vip: {{ old('is_vip', $table?->is_vip ?? false) ? 'true' : 'false' }} }">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    {{-- Nama --}}
    <div>
        <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Meja</label>
        <input type="text" id="name" name="name" value="{{ old('name', $table?->name) }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-brand focus:ring-brand @error('name') border-red-300 @enderror"
               placeholder="mis. Meja 7" />
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Meja VIP (BR-015 — diprioritaskan di antrian station) --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700">Meja VIP</label>
        {{-- hidden input yang dikirim ke server; toggle cuma mengubah nilainya --}}
        <input type="hidden" name="is_vip" :value="vip ? 1 : 0" />
        <div class="flex items-center gap-3">
            <button type="button" @click="vip = !vip"
                    :class="vip ? 'bg-amber-400' : 'bg-slate-300'"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="vip ? 'translate-x-6' : 'translate-x-1'"
                      class="inline-block h-4 w-4 rounded-full bg-white transition-transform"></span>
            </button>
            <span class="text-sm font-medium" :class="vip ? 'text-amber-600' : 'text-slate-400'"
                  x-text="vip ? 'VIP' : 'Reguler'"></span>
        </div>
        <p class="mt-1.5 text-xs text-slate-400">Order dari meja VIP tampil lebih dulu di antrian Kitchen/Barista.</p>
        @error('is_vip')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status: hanya informasi — dikelola sistem (FR-004), tidak bisa diubah admin --}}
    @if ($isEdit)
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Status Saat Ini</label>
            <x-table-status-badge :status="$table->status" />
        </div>
    @endif

    <div class="flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
        <x-lucide-info class="mt-0.5 h-4 w-4 shrink-0" />
        <p>
            Status meja dikelola otomatis oleh sistem: menjadi <strong>Terisi</strong> saat order dibuat
            dan kembali <strong>Kosong</strong> setelah pembayaran selesai. Status tidak bisa diubah manual.
        </p>
    </div>

    {{-- Aksi --}}
    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.tables.index') }}"
           class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
            Batal
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-light">
            <x-lucide-check class="h-4 w-4" />
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Meja' }}
        </button>
    </div>
</form>
