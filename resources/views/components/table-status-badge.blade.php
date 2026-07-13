@props(['status'])

{{-- Badge status meja — hardcode per-status (purge-safe), pola station-badge/role-badge. --}}
@switch($status)
    @case('terisi')
        <span class="inline-flex items-center rounded-full bg-brand px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-white">Terisi</span>
        @break
    @case('kosong')
        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-emerald-700">Kosong</span>
        @break
    @default
        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-slate-600">{{ ucfirst($status) }}</span>
@endswitch
