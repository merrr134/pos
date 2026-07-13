@php
    $category = $category ?? null;
    $isEdit   = (bool) $category;
    $action   = $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store');
@endphp

<form action="{{ $action }}" method="POST" class="space-y-5">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    {{-- Nama --}}
    <div>
        <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Kategori</label>
        <input type="text" id="name" name="name" value="{{ old('name', $category?->name) }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-brand focus:ring-brand @error('name') border-red-300 @enderror"
               placeholder="mis. Espresso Based" />
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Station --}}
    <div>
        <label for="station" class="mb-1.5 block text-sm font-medium text-slate-700">Station Tujuan</label>
        <select id="station" name="station"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-brand focus:ring-brand @error('station') border-red-300 @enderror">
            <option value="" disabled @selected(! old('station', $category?->station))>— Pilih station —</option>
            <option value="kitchen" @selected(old('station', $category?->station) === 'kitchen')>Kitchen (makanan)</option>
            <option value="barista" @selected(old('station', $category?->station) === 'barista')>Barista (minuman)</option>
        </select>
        <p class="mt-1.5 text-xs text-slate-400">
            Item order dari kategori ini akan otomatis masuk ke antrian station yang dipilih.
        </p>
        @error('station')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @if ($isEdit && $category->menus()->exists())
        <div class="flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
            <x-lucide-info class="mt-0.5 h-4 w-4 shrink-0" />
            <p>
                Mengubah station hanya berlaku untuk <strong>order baru</strong>. Item pada order yang sudah dibuat
                tetap mengikuti station saat order tersebut dibuat.
            </p>
        </div>
    @endif

    {{-- Aksi --}}
    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.categories.index') }}"
           class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
            Batal
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-light">
            <x-lucide-check class="h-4 w-4" />
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kategori' }}
        </button>
    </div>
</form>
