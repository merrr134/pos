@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
    {{-- Hero header — WAJIB gradient brand --}}
    <div class="hero-header mb-6">
        <h2 class="font-display text-2xl font-bold">Kasir</h2>
        <p class="mt-1 text-sm text-white/80">Cari tagihan, proses pembayaran, dan cetak struk pelanggan.</p>
    </div>

    {{-- Ringkasan error validasi (server-side) --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
            <div class="flex items-start gap-3">
                <x-lucide-alert-circle class="mt-0.5 h-5 w-5 shrink-0 text-red-500" />
                <div class="text-sm text-red-700">
                    <p class="font-semibold">Pembayaran belum bisa diproses:</p>
                    <ul class="mt-1 list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Kartu sukses pasca-bayar + tombol cetak struk --}}
    @if ($justPaid)
        <div class="mb-6 flex flex-col gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                    <x-lucide-check-circle-2 class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Pembayaran {{ $justPaid->order_number }} selesai</p>
                    <p class="text-xs text-slate-500">{{ $justPaid->table->name }} — {{ $justPaid->customer_name }} · Total Rp{{ number_format($justPaid->total, 0, ',', '.') }}</p>
                </div>
            </div>
            <a href="{{ route('cashier.receipt', $justPaid) }}" target="_blank"
               class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-light">
                <x-lucide-printer class="h-4 w-4" /> Cetak Ulang Struk
            </a>
        </div>

        {{--
            AUTO PRINT (popup anti popup-blocker):
            popup bernama "pitou_receipt" sudah dibuka SINKRON saat kasir klik "Bayar"
            (user gesture — tidak diblokir Chrome). Di sini popup itu tinggal
            diarahkan ke URL receipt; onload receipt memanggil window.print() dan
            menutup dirinya sendiri setelah dialog print selesai (afterprint).
            Kasir tetap berada di halaman kasir dengan data yang sudah ter-refresh.
            Tombol di atas = fallback/reprint.
        --}}
        <script>
            (() => {
                const url = @js(route('cashier.receipt', $justPaid));
                const popup = window.open(url, 'pitou_receipt', 'width=420,height=640');
                if (!popup) {
                    // Fallback (mis. halaman ?paid= dibuka/di-refresh langsung tanpa klik Bayar,
                    // sehingga popup diblokir): iframe tersembunyi seperti mekanisme lama.
                    const frame = document.createElement('iframe');
                    frame.title = 'Cetak struk otomatis';
                    frame.src = url;
                    frame.style.cssText = 'position:absolute;width:0;height:0;border:0;visibility:hidden;';
                    document.body.appendChild(frame);
                }
            })();
        </script>
    @endif

    @if ($errors->any())
        {{-- Pembayaran gagal validasi: tutup popup struk kosong yang terlanjur dibuka saat klik "Bayar". --}}
        <script>
            (() => {
                try {
                    const w = window.open('', 'pitou_receipt');
                    if (w && w.location.href === 'about:blank') w.close();
                } catch (e) { /* popup tidak ada / diblokir — tidak apa-apa */ }
            })();
        </script>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- ================= KOLOM KIRI: Cari Tagihan ================= --}}
        <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="font-semibold text-slate-800">Cari Tagihan</h3>
                    <span class="text-xs text-slate-400">{{ $orders->count() }} order aktif</span>
                </div>

                <form method="GET" action="{{ route('cashier.index') }}" class="mb-4 flex items-center gap-2">
                    <div class="relative flex-1">
                        <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Cari berdasarkan nomor meja, nama customer, atau nomor order..."
                               class="w-full rounded-lg border border-slate-200 py-2.5 pl-9 pr-3 text-sm focus:border-brand focus:ring-brand" />
                    </div>
                    <button type="submit"
                            class="rounded-lg bg-brand px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-light">
                        Cari
                    </button>
                </form>

                {{-- Grid kartu order aktif --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @forelse ($orders as $order)
                        @php($isSelected = $selected && $selected->id === $order->id)
                        <div class="rounded-xl border bg-white p-4 {{ $isSelected ? 'border-brand ring-2 ring-brand/20' : 'border-slate-200' }}">
                            <div class="flex items-start justify-between">
                                <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $order->order_number }}</span>
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-800">
                                    {{ $order->table->name }}
                                </span>
                            </div>
                            <p class="mt-1.5 font-display text-lg font-bold text-slate-800">{{ $order->customer_name }}</p>
                            <p class="text-xs text-slate-400">Total Item: {{ $order->items->sum('qty') }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <p class="text-lg font-bold text-brand">Rp{{ number_format($order->total, 0, ',', '.') }}</p>
                                @if ($isSelected)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand px-3 py-1.5 text-xs font-semibold text-white">
                                        <x-lucide-check class="h-3.5 w-3.5" /> Dipilih
                                    </span>
                                @else
                                    <a href="{{ route('cashier.index', array_filter(['search' => $search, 'order' => $order->id])) }}"
                                       class="rounded-full border border-brand/30 bg-brand/5 px-4 py-1.5 text-xs font-semibold text-brand hover:bg-brand hover:text-white">
                                        Pilih
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-10 text-center">
                            <x-lucide-receipt class="mx-auto h-8 w-8 text-slate-200" />
                            <p class="mt-2 text-sm text-slate-400">
                                {{ $search ? 'Tidak ada tagihan yang cocok dengan pencarian.' : 'Tidak ada tagihan aktif. Semua pembayaran sudah selesai.' }}
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Riwayat Hari Ini (reprint struk diperbolehkan) --}}
            <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="mb-3 flex items-center gap-2">
                    <x-lucide-history class="h-4 w-4 text-slate-400" />
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Riwayat Hari Ini</h3>
                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">{{ $todayPayments->count() }}</span>
                </div>
                @if ($todayPayments->isEmpty())
                    <p class="py-3 text-center text-sm text-slate-400">Belum ada pembayaran hari ini.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($todayPayments as $payment)
                            <a href="{{ route('cashier.receipt', $payment->order) }}" target="_blank"
                               title="Cetak ulang struk {{ $payment->order->order_number }}"
                               class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs text-slate-600 hover:border-brand hover:text-brand">
                                <x-lucide-check-circle-2 class="h-3.5 w-3.5 text-emerald-500" />
                                <span class="font-semibold">{{ $payment->order->order_number }}</span>
                                <span>Rp{{ number_format($payment->amount_paid - $payment->change, 0, ',', '.') }}</span>
                                <span class="text-slate-400">· {{ $payment->paid_at->format('H:i') }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ================= PANEL KANAN: Detail Pembayaran ================= --}}
        <div>
            @if ($selected)
                {{-- BR-016: pajak dari Settings (Admin) — nilai final tetap dihitung ulang server saat submit. --}}
                {{-- Catatan: gunakan bentuk direktif php INLINE saja di file ini (bentuk blok
                     ber-pasangan penutup salah ter-pairing oleh compiler bila bercampur inline). --}}
                @php($taxAmount = (int) round($selected->total * $taxPercent / 100))
                @php($grandTotal = (int) $selected->total + $taxAmount)
                @php($taxLabel = rtrim(rtrim(number_format($taxPercent, 2, ',', '.'), '0'), ','))
                <div class="sticky top-6 overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm"
                     x-data="paymentPanel({ total: {{ $grandTotal }} })">

                    {{-- Header --}}
                    <div class="border-b border-slate-100 bg-cream p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800">Detail Pembayaran</h3>
                            <a href="{{ route('cashier.index', array_filter(['search' => $search])) }}"
                               class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600" title="Tutup">
                                <x-lucide-x class="h-4 w-4" />
                            </a>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <div>
                                <p class="font-display text-lg font-bold text-slate-800">{{ $selected->table->name }}</p>
                                <p class="text-xs text-slate-500">Customer: {{ $selected->customer_name }}</p>
                            </div>
                            <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $selected->order_number }}</span>
                        </div>
                    </div>

                    {{-- Rincian item --}}
                    <div class="max-h-56 overflow-y-auto border-b border-slate-100 p-4">
                        <div class="space-y-3">
                            @foreach ($selected->items as $item)
                                <div class="flex items-start justify-between gap-3 text-sm">
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $item->menu->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $item->qty }} × Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                    <p class="font-semibold text-slate-700">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 space-y-1.5 border-t border-dashed border-slate-200 pt-3">
                            <div class="flex items-center justify-between text-sm text-slate-500">
                                <span>Subtotal</span>
                                <span>Rp{{ number_format($selected->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm text-slate-500">
                                <span>Pajak ({{ $taxLabel }}%)</span>
                                <span>Rp{{ number_format($taxAmount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <span class="font-semibold text-slate-800">Grand Total</span>
                                <span class="text-lg font-bold text-brand">Rp{{ number_format($grandTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Form pembayaran --}}
                    <form method="POST" action="{{ route('payments.store') }}" class="space-y-4 p-4"
                          @submit="if (!canPay) { $event.preventDefault() } else { openReceiptPopup() }">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $selected->id }}" />
                        <input type="hidden" name="payment_method" :value="method" />
                        <input type="hidden" name="amount_paid" :value="amount || 0" />

                        {{-- Metode pembayaran (sesuai enum skema: cash | qris) --}}
                        <div>
                            <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400">Metode Pembayaran</p>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" @click="method = 'cash'"
                                        :class="method === 'cash' ? 'border-brand bg-brand/5 text-brand' : 'border-slate-200 text-slate-500 hover:border-slate-300'"
                                        class="flex items-center justify-center gap-2 rounded-lg border px-3 py-2.5 text-sm font-semibold">
                                    <x-lucide-banknote class="h-4 w-4" /> Cash
                                </button>
                                <button type="button" @click="method = 'qris'"
                                        :class="method === 'qris' ? 'border-brand bg-brand/5 text-brand' : 'border-slate-200 text-slate-500 hover:border-slate-300'"
                                        class="flex items-center justify-center gap-2 rounded-lg border px-3 py-2.5 text-sm font-semibold">
                                    <x-lucide-qr-code class="h-4 w-4" /> QRIS
                                </button>
                            </div>
                        </div>

                        {{-- Badge QRIS (BR-017): nominal otomatis = grand total, kasir tidak input apa pun --}}
                        <div x-show="method === 'qris'" x-cloak
                             class="flex items-start gap-2 rounded-lg border border-emerald-200 bg-emerald-50 p-3">
                            <x-lucide-check-circle-2 class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" />
                            <div class="text-xs text-emerald-700">
                                <p class="font-semibold">Pembayaran QRIS</p>
                                <p>Nominal dibayar otomatis sesuai Grand Total.</p>
                            </div>
                        </div>

                        {{-- Nominal bayar + saran pecahan Rupiah (FR-008) — KHUSUS CASH (BR-017) --}}
                        <div x-show="method === 'cash'">
                            <label for="amount_input" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-400">Nominal Bayar</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                                <input type="number" id="amount_input" min="0" step="500" x-model.number="amount"
                                       placeholder="0"
                                       class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-3 text-sm font-semibold focus:border-brand focus:ring-brand" />
                            </div>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <template x-for="s in suggestions" :key="s">
                                    <button type="button" @click="amount = s"
                                            :class="amount === s ? 'border-brand bg-brand text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand'"
                                            class="rounded-full border px-3 py-1 text-xs font-semibold"
                                            x-text="s === total ? 'Uang Pas' : rupiah(s)"></button>
                                </template>
                            </div>
                        </div>

                        {{-- Kembalian (preview client; nilai final dihitung server) — KHUSUS CASH (BR-017) --}}
                        <div x-show="method === 'cash'" class="flex items-center justify-between rounded-lg p-3"
                             :class="sufficient ? 'bg-emerald-50' : 'bg-rose-50'">
                            <span class="text-sm font-medium" :class="sufficient ? 'text-emerald-700' : 'text-rose-600'">
                                <span x-show="sufficient">Kembalian</span>
                                <span x-show="!sufficient" x-cloak>Kurang</span>
                            </span>
                            <span class="text-lg font-bold" :class="sufficient ? 'text-emerald-700' : 'text-rose-600'"
                                  x-text="rupiah(Math.abs((amount || 0) - total))"></span>
                        </div>

                        {{-- Preview struk mini (Figma) --}}
                        <div class="rounded-lg border border-dashed border-slate-200 bg-slate-50 p-3 font-mono text-[11px] text-slate-500">
                            <p class="text-center font-bold text-slate-700">PITOU CAFE</p>
                            <p class="text-center">{{ $selected->order_number }} · {{ $selected->table->name }}</p>
                            <div class="my-1.5 border-t border-dashed border-slate-300"></div>
                            <div class="flex justify-between"><span>Subtotal</span><span>Rp{{ number_format($selected->total, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span>Pajak ({{ $taxLabel }}%)</span><span>Rp{{ number_format($taxAmount, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between font-bold text-slate-700"><span>Grand Total</span><span>Rp{{ number_format($grandTotal, 0, ',', '.') }}</span></div>
                            {{-- Cash: Bayar + Kembalian; QRIS: Metode + LUNAS (BR-017) --}}
                            <template x-if="method === 'cash'">
                                <div>
                                    <div class="flex justify-between"><span>Bayar (CASH)</span><span x-text="rupiah(amount || 0)"></span></div>
                                    <div class="flex justify-between font-bold text-slate-700"><span>Kembalian</span><span x-text="sufficient ? rupiah((amount || 0) - total) : '—'"></span></div>
                                </div>
                            </template>
                            <template x-if="method === 'qris'">
                                <div>
                                    <div class="flex justify-between"><span>Metode Pembayaran</span><span>QRIS</span></div>
                                    <div class="flex justify-between font-bold text-emerald-700"><span>Status Pembayaran</span><span>LUNAS</span></div>
                                </div>
                            </template>
                        </div>

                        <button type="submit" :disabled="!canPay"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-light disabled:cursor-not-allowed disabled:opacity-40">
                            <x-lucide-check-circle-2 class="h-4 w-4" /> Bayar & Selesaikan
                        </button>
                        <p x-show="!canPay && method === 'cash'" x-cloak class="text-center text-[11px] text-slate-400">
                            Nominal bayar harus ≥ grand total setelah pajak (BR-007).
                        </p>
                    </form>
                </div>
            @else
                {{-- Belum ada order dipilih --}}
                <div class="sticky top-6 rounded-xl border-2 border-dashed border-slate-200 bg-white/60 p-8 text-center">
                    <x-lucide-mouse-pointer-click class="mx-auto h-8 w-8 text-slate-300" />
                    <h3 class="mt-3 font-semibold text-slate-500">Pilih Tagihan</h3>
                    <p class="mx-auto mt-1 max-w-xs text-sm text-slate-400">
                        Klik "Pilih" pada salah satu order aktif untuk menampilkan detail pembayaran di sini.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        /**
         * Panel pembayaran kasir (FR-008).
         * Saran pecahan Rupiah: "Uang Pas" + pembulatan total ke pecahan umum di atasnya.
         * Kembalian di sini hanya PREVIEW — nilai final dihitung server (BR-007).
         */
        Alpine.data('paymentPanel', ({ total }) => ({
            total,
            amount: null,
            method: 'cash',

            get suggestions() {
                const set = new Set([this.total]); // uang pas
                for (const d of [1000, 2000, 5000, 10000, 20000, 50000, 100000]) {
                    const rounded = Math.ceil(this.total / d) * d;
                    if (rounded > this.total) set.add(rounded);
                }
                return [...set].sort((a, b) => a - b).slice(0, 5);
            },

            get sufficient() {
                return (this.amount || 0) >= this.total;
            },

            get canPay() {
                // BR-017: QRIS selalu pas sebesar grand total — tidak butuh input nominal.
                if (this.method === 'qris') return true;
                return this.sufficient; // cash: BR-007
            },

            rupiah(n) {
                return 'Rp' + Number(n).toLocaleString('id-ID');
            },

            openReceiptPopup() {
                // Dibuka SINKRON dari klik "Bayar" (user gesture) sebelum form ter-submit,
                // sehingga Chrome tidak memblokirnya. Setelah pembayaran sukses, halaman
                // kasir hasil redirect mengarahkan popup bernama ini ke URL struk.
                const w = window.open('about:blank', 'pitou_receipt', 'width=420,height=640');
                if (w) {
                    try {
                        w.document.write('<p style="font-family:sans-serif;padding:16px;color:#555">Memproses pembayaran &amp; menyiapkan struk…</p>');
                    } catch (e) { /* abaikan */ }
                }
            },
        }));
    });
</script>
@endpush
