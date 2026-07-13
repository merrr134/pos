{{--
    Modal konfirmasi hapus GLOBAL — Alpine store $store.deleteModal.
    Include SEKALI di layout. Cara pakai dari mana saja:

        <button type="button"
            @click="$store.deleteModal.trigger('{{ route('admin.users.destroy', $user) }}', '{{ $user->name }}')">
            Hapus
        </button>

    Store menyimpan URL action; konfirmasi men-submit form DELETE (CSRF + _method).
--}}
<div
    x-data
    x-show="$store.deleteModal.open"
    x-cloak
    @keydown.escape.window="$store.deleteModal.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    {{-- Backdrop --}}
    <div
        x-show="$store.deleteModal.open"
        x-transition.opacity
        @click="$store.deleteModal.close()"
        class="absolute inset-0 bg-black/50"
    ></div>

    {{-- Panel --}}
    <div
        x-show="$store.deleteModal.open"
        x-transition
        @click.stop
        class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl"
    >
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
            <x-lucide-trash-2 class="h-6 w-6" />
        </div>

        <h3 class="mt-4 text-center font-display text-lg font-bold text-slate-800">
            Hapus Data?
        </h3>
        <p class="mt-1 text-center text-sm text-slate-500">
            Anda yakin ingin menghapus
            <span class="font-semibold text-slate-700" x-text="$store.deleteModal.name || 'data ini'"></span>?
            <br>Tindakan ini tidak bisa dibatalkan.
        </p>

        <form :action="$store.deleteModal.url" method="POST" class="mt-6 flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button"
                @click="$store.deleteModal.close()"
                class="flex-1 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Batal
            </button>
            <button type="submit"
                class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('deleteModal', {
                    open: false,
                    url: null,
                    name: null,

                    trigger(url, name = null) {
                        this.url  = url;
                        this.name = name;
                        this.open = true;
                    },

                    close() {
                        this.open = false;
                        this.url  = null;
                        this.name = null;
                    },
                });
            });
        </script>
    @endpush
@endonce
