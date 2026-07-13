@php($u = auth()->user())
<header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-20">

    {{-- Kiri: hamburger (mobile) + judul --}}
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = true" class="lg:hidden text-slate-500">
            <x-lucide-menu class="w-6 h-6" />
        </button>
        <h1 class="text-lg font-semibold text-slate-800">@yield('title', 'Dashboard')</h1>
    </div>

    {{-- Kanan: user + logout --}}
    <div class="flex items-center gap-4" x-data="{ open: false }">
        <div class="text-right hidden sm:block">
            <p class="text-sm font-medium text-slate-800 leading-tight">{{ $u->name }}</p>
            <p class="text-[11px] uppercase tracking-wide text-slate-400">{{ ucfirst($u->role) }}</p>
        </div>

        <div class="relative">
            <button @click="open = !open"
                    class="w-9 h-9 rounded-full bg-gradient-to-br from-brand to-brand-light text-white
                           flex items-center justify-center text-sm font-semibold">
                {{ strtoupper(substr($u->name, 0, 1)) }}
            </button>

            <div x-show="open" x-cloak @click.outside="open = false"
                 class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg border border-slate-100 py-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <x-lucide-log-out class="w-4 h-4" /> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
