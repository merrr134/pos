@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
    {{-- Hero header — WAJIB gradient brand (pakai utility .hero-header project) --}}
    <div class="hero-header mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold">Kelola User</h2>
            <p class="mt-1 text-sm text-white/80">Atur hak akses, role, dan status aktif seluruh tim operasional Anda.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-brand shadow-sm hover:bg-white/90">
            <x-lucide-plus class="h-4 w-4" /> Tambah User Baru
        </a>
    </div>

    {{-- Kartu statistik --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand/10 text-brand">
                    <x-lucide-users class="h-5 w-5" />
                </div>
                @if ($stats['new'] > 0)
                    <span class="text-xs font-medium text-emerald-600">+{{ $stats['new'] }} baru</span>
                @endif
            </div>
            <p class="mt-4 text-sm text-slate-500">Total User</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand/10 text-brand">
                <x-lucide-shield class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Admin</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $stats['admin'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand/10 text-brand">
                <x-lucide-calculator class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Kasir</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $stats['kasir'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand/10 text-brand">
                <x-lucide-utensils-crossed class="h-5 w-5" />
            </div>
            <p class="mt-4 text-sm text-slate-500">Waiters</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $stats['waiters'] }}</p>
        </div>
    </div>

    {{-- Kartu tabel --}}
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">

        {{-- Toolbar: search + filter (GET) --}}
        <form method="GET" action="{{ route('admin.users.index') }}"
              class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-xs">
                <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..."
                       class="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-brand focus:ring-brand" />
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select name="role" onchange="this.form.submit()"
                        class="rounded-lg border border-slate-200 py-2 pl-3 pr-8 text-sm focus:border-brand focus:ring-brand">
                    <option value="">Semua Role</option>
                    <option value="admin"   @selected($role === 'admin')>Admin</option>
                    <option value="waiters" @selected($role === 'waiters')>Waiter</option>
                    <option value="kitchen" @selected($role === 'kitchen')>Kitchen</option>
                    <option value="barista" @selected($role === 'barista')>Barista</option>
                    <option value="kasir"   @selected($role === 'kasir')>Kasir</option>
                </select>

                <select name="status" onchange="this.form.submit()"
                        class="rounded-lg border border-slate-200 py-2 pl-3 pr-8 text-sm focus:border-brand focus:ring-brand">
                    <option value="">Status: Semua</option>
                    <option value="active"   @selected($status === 'active')>Aktif</option>
                    <option value="inactive" @selected($status === 'inactive')>Non-aktif</option>
                </select>

                <button type="submit"
                        class="rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white hover:bg-brand-light">
                    Cari
                </button>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-medium">No</th>
                        <th class="px-5 py-3 font-medium">Nama Lengkap</th>
                        <th class="px-5 py-3 font-medium">Role</th>
                        <th class="px-5 py-3 font-medium">Email</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        @php
                            $isSelf      = $user->id === auth()->id();
                            $isLastAdmin = $user->role === 'admin' && $stats['admin'] <= 1;
                            $lockDelete  = $isSelf || $isLastAdmin;
                        @endphp
                        <tr class="text-slate-700">
                            <td class="px-5 py-3 text-slate-400">
                                {{ str_pad($users->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand/10 text-sm font-semibold text-brand">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                    <span class="font-medium text-slate-800">{{ $user->name }}</span>
                                </div>
                            </td>

                            <td class="px-5 py-3">
                                <x-role-badge :role="$user->role" />
                            </td>

                            <td class="px-5 py-3 text-slate-500">{{ $user->email }}</td>

                            {{-- Status: toggle (hardcode per-state, purge-safe) --}}
                            <td class="px-5 py-3">
                                @if ($isSelf)
                                    <div class="inline-flex items-center gap-2" title="Tidak bisa mengubah status akun sendiri">
                                        <span class="relative inline-flex h-6 w-11 cursor-not-allowed items-center rounded-full bg-brand/40">
                                            <span class="inline-block h-4 w-4 translate-x-6 rounded-full bg-white"></span>
                                        </span>
                                        <span class="text-xs font-medium text-slate-400">Aktif</span>
                                    </div>
                                @elseif ($user->is_active)
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Nonaktifkan user"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full bg-brand transition-colors">
                                            <span class="inline-block h-4 w-4 translate-x-6 rounded-full bg-white transition-transform"></span>
                                        </button>
                                        <span class="text-xs font-medium text-brand">Aktif</span>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Aktifkan user"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full bg-slate-300 transition-colors">
                                            <span class="inline-block h-4 w-4 translate-x-1 rounded-full bg-white transition-transform"></span>
                                        </button>
                                        <span class="text-xs font-medium text-slate-400">Non-aktif</span>
                                    </form>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-brand" title="Edit">
                                        <x-lucide-pencil class="h-4 w-4" />
                                    </a>

                                    @if ($lockDelete)
                                        <span class="cursor-not-allowed rounded-lg p-2 text-slate-200"
                                              title="{{ $isSelf ? 'Tidak bisa menghapus akun sendiri' : 'Admin terakhir tidak bisa dihapus' }}">
                                            <x-lucide-trash-2 class="h-4 w-4" />
                                        </span>
                                    @else
                                        <button type="button"
                                                @click="$store.deleteModal.trigger('{{ route('admin.users.destroy', $user) }}', @js($user->name))"
                                                class="rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600" title="Hapus">
                                            <x-lucide-trash-2 class="h-4 w-4" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">
                                Tidak ada user yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: info + pagination --}}
        <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-400">
                Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} user
            </p>
            <div>{{ $users->onEachSide(1)->links() }}</div>
        </div>
    </div>

    {{-- Info cards (Figma) --}}
    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-5">
            <div class="flex items-start gap-3">
                <div class="rounded-lg bg-amber-100 p-2 text-amber-700"><x-lucide-shield class="h-5 w-5" /></div>
                <div>
                    <h4 class="font-semibold text-slate-800">Tip Keamanan</h4>
                    <p class="mt-1 text-sm text-slate-500">
                        Pastikan role 'Admin' hanya diberikan kepada manajer atau owner untuk menjaga integritas data laporan keuangan Anda.
                    </p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-5">
            <div class="flex items-start gap-3">
                <div class="rounded-lg bg-brand/10 p-2 text-brand"><x-lucide-info class="h-5 w-5" /></div>
                <div>
                    <h4 class="font-semibold text-slate-800">Status User</h4>
                    <p class="mt-1 text-sm text-slate-500">
                        Menonaktifkan user akan mencabut akses mereka ke aplikasi seketika tanpa menghapus riwayat transaksi mereka di sistem.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
