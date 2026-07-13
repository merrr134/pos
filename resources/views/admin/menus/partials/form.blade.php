@php
    $menu   = $menu ?? null;
    $isEdit = (bool) $menu;
    $action = $isEdit ? route('admin.menus.update', $menu) : route('admin.menus.store');
@endphp

<form
    action="{{ $action }}"
    method="POST"
    enctype="multipart/form-data"
    x-data="{
        available: {{ old('is_available', $menu?->is_available ?? true) ? 'true' : 'false' }},
        preview: @js($menu?->image ? asset('storage/' . $menu->image) : null),
        pickImage(event) {
            const file = event.target.files[0];
            if (file) this.preview = URL.createObjectURL(file);
        }
    }"
    class="space-y-5"
>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    {{-- Foto menu --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700">
            Foto Menu <span class="font-normal text-slate-400">(opsional, maks. 2 MB)</span>
        </label>
        <div class="flex items-start gap-4">
            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                <template x-if="preview">
                    <img :src="preview" alt="Preview" class="h-full w-full object-cover" />
                </template>
                <template x-if="!preview">
                    <x-lucide-image class="h-8 w-8 text-slate-300" />
                </template>
            </div>
            <div class="flex-1">
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                       @change="pickImage($event)"
                       class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand/10 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand hover:file:bg-brand/20" />
                <p class="mt-1.5 text-xs text-slate-400">
                    Format JPG, PNG, atau WEBP.
                    @if ($isEdit)
                        Kosongkan jika tidak ingin mengganti foto.
                    @endif
                </p>
                @error('image')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Nama --}}
    <div>
        <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Menu</label>
        <input type="text" id="name" name="name" value="{{ old('name', $menu?->name) }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-brand focus:ring-brand @error('name') border-red-300 @enderror"
               placeholder="mis. Kopi Susu Gula Aren" />
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Kategori + Harga --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label for="category_id" class="mb-1.5 block text-sm font-medium text-slate-700">Kategori</label>
            <select id="category_id" name="category_id"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-brand focus:ring-brand @error('category_id') border-red-300 @enderror">
                <option value="" disabled @selected(! old('category_id', $menu?->category_id))>— Pilih kategori —</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((string) old('category_id', $menu?->category_id) === (string) $cat->id)>
                        {{ $cat->name }} ({{ $cat->station === 'kitchen' ? 'Kitchen' : 'Barista' }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1.5 text-xs text-slate-400">Kategori menentukan station tujuan pesanan (Kitchen/Barista).</p>
            @error('category_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="price" class="mb-1.5 block text-sm font-medium text-slate-700">Harga</label>
            <div class="relative">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                <input type="number" id="price" name="price" min="0" step="500"
                       value="{{ old('price', $menu ? (int) $menu->price : null) }}"
                       class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-3 text-sm focus:border-brand focus:ring-brand @error('price') border-red-300 @enderror"
                       placeholder="mis. 18000" />
            </div>
            @error('price')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Deskripsi --}}
    <div>
        <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">
            Deskripsi <span class="font-normal text-slate-400">(opsional)</span>
        </label>
        <textarea id="description" name="description" rows="3"
                  class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-brand focus:ring-brand @error('description') border-red-300 @enderror"
                  placeholder="Deskripsi singkat menu, mis. Signature Indonesian coffee">{{ old('description', $menu?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status ketersediaan --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700">Status</label>
        {{-- hidden input yang dikirim ke server; toggle cuma mengubah nilainya --}}
        <input type="hidden" name="is_available" :value="available ? 1 : 0" />
        <div class="flex items-center gap-3">
            <button type="button" @click="available = !available"
                    :class="available ? 'bg-brand' : 'bg-slate-300'"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="available ? 'translate-x-6' : 'translate-x-1'"
                      class="inline-block h-4 w-4 rounded-full bg-white transition-transform"></span>
            </button>
            <span class="text-sm font-medium" :class="available ? 'text-brand' : 'text-slate-400'"
                  x-text="available ? 'Tersedia' : 'Habis'"></span>
        </div>
        <p class="mt-1.5 text-xs text-slate-400">Menu berstatus "Habis" tidak bisa dipilih Waiters saat membuat order.</p>
    </div>

    {{-- Aksi --}}
    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.menus.index') }}"
           class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
            Batal
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-light">
            <x-lucide-check class="h-4 w-4" />
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Menu' }}
        </button>
    </div>
</form>
