@if (session('success') || session('error'))
    <div class="mb-6 space-y-3">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 x-init="setTimeout(() => show = false, 4000)"
                 class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <x-lucide-check class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
                <p class="flex-1">{{ session('success') }}</p>
                <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                    <x-lucide-x class="h-4 w-4" />
                </button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <x-lucide-info class="mt-0.5 h-5 w-5 shrink-0 text-red-600" />
                <p class="flex-1">{{ session('error') }}</p>
                <button type="button" @click="show = false" class="text-red-500 hover:text-red-700">
                    <x-lucide-x class="h-4 w-4" />
                </button>
            </div>
        @endif
    </div>
@endif
