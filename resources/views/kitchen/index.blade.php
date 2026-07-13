@extends('layouts.app')

@section('title', 'Kitchen')

@section('content')
<div x-data="stationQueue({ lastId: {{ $lastItemId }}, signature: @js($signature), unprintedCount: {{ $unprinted->count() }} })">

    {{--
        Hero header (WAJIB gradient brand) — di LUAR container partial refresh:
        header, jam, dan toggle suara tidak pernah di-render ulang oleh polling.
        Badge jumlah dibuat reaktif via x-text (update angka, bukan re-render).
    --}}
    <div class="hero-header mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold">Station Kitchen</h2>
            <p class="mt-1 text-sm text-white/80">Antrian item makanan dari order aktif. Layar diperbarui otomatis.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-white/15 px-3 py-2 text-sm font-semibold">
                <x-lucide-chef-hat class="h-4 w-4" />
                <span x-text="unprintedCount">{{ $unprinted->count() }}</span>&nbsp;belum dicetak
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-white/15 px-3 py-2 text-sm font-semibold tabular-nums">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                <span x-text="clock">--:--</span>
            </span>
            {{-- Toggle suara: sekaligus meng-unlock audio context (kebijakan autoplay browser) --}}
            <button type="button" @click="toggleSound()"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-brand hover:bg-white/90">
                <x-lucide-bell x-show="soundOn" class="h-4 w-4" />
                <x-lucide-bell-off x-show="!soundOn" x-cloak class="h-4 w-4" />
                <span x-text="soundOn ? 'Suara: Aktif' : 'Suara: Mati'"></span>
            </button>
        </div>
    </div>

    {{-- Container PARTIAL REFRESH — hanya isi div ini yang di-swap saat antrian berubah --}}
    <div id="kitchen-queue">
        @include('kitchen.partials.queue')
    </div>
</div>
@endsection

@push('scripts')
    {{-- Script polling SHARED station (POLL_INTERVAL satu tempat) --}}
    @include('station.partials.queue-script', [
        'pollUrl'     => route('kitchen.queue-status'),
        'containerId' => 'kitchen-queue',
    ])
@endpush
