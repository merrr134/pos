@props(['station'])

{{-- Badge station — hardcode per-station (purge-safe), mengikuti pola role-badge. --}}
@switch($station)
    @case('kitchen')
        <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-semibold text-orange-700">
            <x-lucide-chef-hat class="h-3 w-3" /> Kitchen
        </span>
        @break
    @case('barista')
        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
            <x-lucide-cup-soda class="h-3 w-3" /> Barista
        </span>
        @break
    @default
        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">{{ ucfirst($station) }}</span>
@endswitch
