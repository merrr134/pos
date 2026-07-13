@php
    $user       = $user ?? null;
    $isEdit     = (bool) $user;
    $action     = $isEdit ? route('admin.users.update', $user) : route('admin.users.store');
    $roleLabels = [
        'admin'   => 'Admin',
        'waiters' => 'Waiter',
        'kitchen' => 'Kitchen',
        'barista' => 'Barista',
        'kasir'   => 'Kasir',
    ];
@endphp

<form
    action="{{ $action }}"
    method="POST"
    x-data="{
        showPassword: false,
        active: {{ old('is_active', $user?->is_active ?? true) ? 'true' : 'false' }}
    }"
    class="space-y-5"
>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    {{-- Nama --}}
    <div>
        <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Lengkap</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user?->name) }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-brand focus:ring-brand @error('name') border-red-300 @enderror"
               placeholder="mis. Budi Santoso" />
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email --}}
    <div>
        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user?->email) }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-brand focus:ring-brand @error('email') border-red-300 @enderror"
               placeholder="mis. budi@artibrew.id" />
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Role --}}
    <div>
        <label for="role" class="mb-1.5 block text-sm font-medium text-slate-700">Role</label>
        <select id="role" name="role"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-brand focus:ring-brand @error('role') border-red-300 @enderror">
            <option value="" disabled @selected(! old('role', $user?->role))>— Pilih role —</option>
            @foreach ($roles as $r)
                <option value="{{ $r }}" @selected(old('role', $user?->role) === $r)>
                    {{ $roleLabels[$r] ?? ucfirst($r) }}
                </option>
            @endforeach
        </select>
        @error('role')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div>
        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">
            Password
            @if ($isEdit)
                <span class="font-normal text-slate-400">(kosongkan jika tidak diubah)</span>
            @endif
        </label>
        <div class="relative">
            <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2.5 pr-10 text-sm focus:border-brand focus:ring-brand @error('password') border-red-300 @enderror"
                   placeholder="Minimal 8 karakter" autocomplete="new-password" />
            <button type="button" @click="showPassword = !showPassword"
                    class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600">
                <x-lucide-eye x-show="!showPassword" class="h-4 w-4" />
                <x-lucide-eye-off x-show="showPassword" x-cloak class="h-4 w-4" />
            </button>
        </div>
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status aktif --}}
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700">Status</label>
        {{-- hidden input yang dikirim ke server; toggle cuma mengubah nilainya --}}
        <input type="hidden" name="is_active" :value="active ? 1 : 0" />
        <div class="flex items-center gap-3">
            <button type="button" @click="active = !active"
                    :class="active ? 'bg-brand' : 'bg-slate-300'"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="active ? 'translate-x-6' : 'translate-x-1'"
                      class="inline-block h-4 w-4 rounded-full bg-white transition-transform"></span>
            </button>
            <span class="text-sm font-medium" :class="active ? 'text-brand' : 'text-slate-400'"
                  x-text="active ? 'Aktif' : 'Non-aktif'"></span>
        </div>
        <p class="mt-1.5 text-xs text-slate-400">User non-aktif tidak bisa login, tapi riwayatnya tetap tersimpan.</p>
    </div>

    {{-- Aksi --}}
    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.users.index') }}"
           class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
            Batal
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-light">
            <x-lucide-check class="h-4 w-4" />
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan User' }}
        </button>
    </div>
</form>
