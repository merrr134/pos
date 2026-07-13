<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Pitou Cafe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    {{-- Background: foto cafe (taruh di public/images/login-bg.jpg) + overlay brand --}}
    <div class="min-h-full bg-cover bg-center relative flex items-center justify-center p-4"
         style="background-image:
                linear-gradient(to bottom right, rgba(124,74,45,.55), rgba(169,113,75,.45)),
                url('{{ asset('images/login-bg.jpg') }}'), linear-gradient(135deg,#7C4A2D,#A9714B);">

        {{-- Card login --}}
        <div class="w-full max-w-md bg-white/95 backdrop-blur rounded-2xl shadow-xl p-8 sm:p-10"
             x-data="{ show: false }">

            {{-- Logo + judul --}}
            <div class="flex flex-col items-center text-center mb-8">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand to-brand-light
                            flex items-center justify-center text-white mb-4 shadow-sm">
                    <x-lucide-coffee class="w-7 h-7" />
                </div>
                <h1 class="font-display text-2xl font-bold text-brand">Pitou Cafe</h1>
                <p class="text-sm text-slate-500 mt-1">Manage your coffee shop easily</p>
            </div>

            {{-- Error umum --}}
            @if ($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-600 mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <x-lucide-mail class="w-5 h-5" />
                        </span>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                               autofocus autocomplete="username"
                               placeholder="email@pitoucafe.test"
                               class="w-full pl-10 pr-3 py-2.5 rounded-lg border-slate-300
                                      focus:border-brand focus:ring-brand text-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-600 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <x-lucide-lock class="w-5 h-5" />
                        </span>
                        <input id="password" name="password"
                               :type="show ? 'text' : 'password'"
                               autocomplete="current-password" placeholder="••••••••"
                               class="w-full pl-10 pr-10 py-2.5 rounded-lg border-slate-300
                                      focus:border-brand focus:ring-brand text-sm">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <x-lucide-eye x-show="!show" class="w-5 h-5" />
                            <x-lucide-eye-off x-show="show" x-cloak class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember"
                               class="rounded border-slate-300 text-brand focus:ring-brand">
                        Remember me
                    </label>
                    <span class="text-brand-light">Forgot password?</span>
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg
                               bg-gradient-to-br from-brand to-brand-light text-white font-semibold text-sm
                               hover:opacity-95 transition shadow-sm">
                    Login
                    <x-lucide-log-in class="w-4 h-4" />
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-6">
                Don't have an account?
                <span class="text-brand font-medium">Contact Admin</span>
            </p>
        </div>
    </div>
</body>
</html>
