@php($u = auth()->user())
{{--
    Sidebar per-role. Blok HTML tiap role DI-HARDCODE (bukan interpolasi class
    dinamis) untuk menghindari Tailwind purge — sesuai aturan proyek.
    Link ke halaman yang belum dibuat sementara diarahkan ke "#".
--}}
<aside
    class="fixed lg:static inset-y-0 left-0 z-40 w-64 shrink-0 bg-gradient-to-b from-brand to-brand-light
           text-white flex flex-col transition-transform duration-200 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    {{-- Brand --}}
    <div class="flex items-center gap-3 px-5 h-16 border-b border-white/10">
        <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
            <x-lucide-coffee class="w-5 h-5" />
        </div>
        <div>
            <p class="font-display font-bold leading-tight">Pitou Cafe</p>
            <p class="text-[11px] uppercase tracking-wide text-white/60">{{ ucfirst($u->role) }}</p>
        </div>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        @if ($u->role === 'admin')
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active' : '' }}">
                <x-lucide-layout-dashboard class="w-5 h-5" /> Dashboard
            </a>
            <a href="{{ route('admin.menus.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.menus.*') ? 'sidebar-link-active' : '' }}">
                <x-lucide-utensils-crossed class="w-5 h-5" /> Menu
            </a>
            <a href="{{ route('admin.categories.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'sidebar-link-active' : '' }}">
                <x-lucide-tags class="w-5 h-5" /> Kategori
            </a>
            <a href="{{ route('admin.tables.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.tables.*') ? 'sidebar-link-active' : '' }}">
                <x-lucide-armchair class="w-5 h-5" /> Meja
            </a>
            <a href="{{ route('admin.transactions.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.transactions.*') ? 'sidebar-link-active' : '' }}">
                <x-lucide-receipt class="w-5 h-5" /> Transaksi
            </a>
            <a href="{{ route('admin.reports.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'sidebar-link-active' : '' }}">
                <x-lucide-bar-chart-3 class="w-5 h-5" /> Reports
            </a>
            <a href="{{ route('admin.users.index') }}"
            class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'sidebar-link-active' : '' }}">
                <x-lucide-users class="w-5 h-5" /> User Management
            </a>
            <a href="{{ route('admin.settings.edit') }}"
               class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'sidebar-link-active' : '' }}">
                <x-lucide-settings class="w-5 h-5" /> Settings
            </a>

        @elseif ($u->role === 'waiters')
            <a href="{{ route('waiter.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('waiter.dashboard') ? 'sidebar-link-active' : '' }}">
                <x-lucide-layout-dashboard class="w-5 h-5" /> Dashboard
            </a>
            {{-- Pilih meja & buat order menyatu di Dashboard (FR-005, satu halaman sesuai Figma) --}}
            <a href="{{ route('waiter.dashboard') }}" class="sidebar-link"><x-lucide-armchair class="w-5 h-5" /> Meja</a>
            <a href="{{ route('waiter.dashboard') }}" class="sidebar-link"><x-lucide-plus-circle class="w-5 h-5" /> Buat Order</a>
            <a href="{{ route('waiter.orders') }}"
               class="sidebar-link {{ request()->routeIs('waiter.orders') ? 'sidebar-link-active' : '' }}">
                <x-lucide-clipboard-list class="w-5 h-5" /> Order Aktif
            </a>
            <a href="{{ route('waiter.menus') }}"
               class="sidebar-link {{ request()->routeIs('waiter.menus') ? 'sidebar-link-active' : '' }}">
                <x-lucide-utensils-crossed class="w-5 h-5" /> Menu (Stok)
            </a>

        @elseif ($u->role === 'kitchen')
            <a href="{{ route('kitchen.index') }}"
               class="sidebar-link {{ request()->routeIs('kitchen.index') ? 'sidebar-link-active' : '' }}">
                <x-lucide-chef-hat class="w-5 h-5" /> Antrian Kitchen
            </a>

        @elseif ($u->role === 'barista')
            <a href="{{ route('barista.index') }}"
               class="sidebar-link {{ request()->routeIs('barista.index') ? 'sidebar-link-active' : '' }}">
                <x-lucide-cup-soda class="w-5 h-5" /> Antrian Barista
            </a>

        @elseif ($u->role === 'kasir')
            <a href="{{ route('cashier.index') }}"
               class="sidebar-link {{ request()->routeIs('cashier.index') ? 'sidebar-link-active' : '' }}">
                <x-lucide-calculator class="w-5 h-5" /> Kasir
            </a>
            <a href="#" class="sidebar-link"><x-lucide-receipt class="w-5 h-5" /> Transaksi</a>
        @endif
    </nav>

    {{-- Profil user (bawah) --}}
    <div class="px-4 py-4 border-t border-white/10 flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-white/15 flex items-center justify-center text-sm font-semibold">
            {{ strtoupper(substr($u->name, 0, 1)) }}
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium truncate">{{ $u->name }}</p>
            <p class="text-[11px] text-white/60 truncate">{{ $u->email }}</p>
        </div>
    </div>
</aside>
