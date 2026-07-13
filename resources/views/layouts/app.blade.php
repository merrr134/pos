<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Pitou Cafe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="min-h-full lg:flex" x-data="{ sidebarOpen: false }">

        {{-- Overlay mobile --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

        {{-- Sidebar --}}
        <x-sidebar />

        {{-- Konten --}}
        <div class="flex-1 flex flex-col min-w-0">
            <x-navbar />

            <main class="flex-1 p-4 sm:p-6">
                <x-flash />
                @yield('content')
            </main>
        </div>
    </div>
    <x-delete-modal />

    @stack('scripts')
</body>
</html>
