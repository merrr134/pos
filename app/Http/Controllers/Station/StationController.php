<?php

namespace App\Http\Controllers\Station;

use App\Http\Controllers\Controller;
use App\Models\CheckerPrint;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Logika bersama station (FR-006 Kitchen / FR-007 Barista) — subclass hanya
 * menentukan station. Konvensi view: "{station}.index" & "{station}.partials.queue".
 *
 * Perilaku yang dijamin identik untuk semua station:
 * - Antrian order aktif ber-item station, dua kelompok Belum/Sudah Dicetak.
 * - BR-015: kelompok Belum Dicetak diurutkan VIP dulu → FIFO.
 * - BR-014: checker sekali cetak (UNIQUE(order_id, station) di checker_prints).
 * - Partial refresh: endpoint JSON ber-signature, html fragment hanya saat berubah.
 */
abstract class StationController extends Controller
{
    /** Station yang ditangani subclass: 'kitchen' | 'barista'. */
    abstract protected function station(): string;

    public function index(): View
    {
        $queue = $this->queueData();

        return view("{$this->station()}.index", $queue + [
            'lastItemId' => $this->lastItemId(),
            'signature'  => $this->signature($queue['unprinted'], $queue['printed']),
        ]);
    }

    /**
     * Endpoint polling (JSON) untuk PARTIAL refresh — dipanggil setInterval tiap POLL_INTERVAL.
     * Klien mengirim signature miliknya; `html` (fragment antrian) hanya disertakan bila
     * signature berbeda → tanpa perubahan = tanpa update DOM di klien.
     * Lonceng dibunyikan klien hanya saat `last_id` naik (item baru), bukan perubahan lain.
     */
    public function queueStatus(Request $request): JsonResponse
    {
        $queue     = $this->queueData();
        $signature = $this->signature($queue['unprinted'], $queue['printed']);

        $payload = [
            'last_id'         => $this->lastItemId(),
            'signature'       => $signature,
            'unprinted_count' => $queue['unprinted']->count(),
        ];

        if ($request->query('signature') !== $signature) {
            $payload['html'] = view("{$this->station()}.partials.queue", $queue)->render();
        }

        return response()->json($payload);
    }

    /**
     * Cetak checker (view shared station.checker).
     * BR-014: hanya SATU KALI per station — insert ke checker_prints; UNIQUE(order_id, station)
     * membuat percobaan kedua gagal atomik (aman terhadap refresh/race dua tab).
     */
    public function checker(Order $order): View
    {
        $station = $this->station();

        $items = $order->items()->where('station', $station)->with('menu')->get();

        abort_if($items->isEmpty(), 404); // order tanpa item station ini tidak punya checker-nya

        try {
            CheckerPrint::create([
                'order_id'   => $order->id,
                'station'    => $station,
                'printed_at' => now(),
            ]);
        } catch (UniqueConstraintViolationException) {
            abort(403, "Checker {$station} untuk order ini sudah pernah dicetak.");
        }

        return view('station.checker', [
            'order'   => $order->load('table'),
            'items'   => $items,
            'station' => $station,
        ]);
    }

    /** Antrian station: order aktif ber-item station ini, dipecah belum/sudah dicetak. */
    protected function queueData(): array
    {
        $station = $this->station();

        $orders = Order::query()
            ->with(['table', 'checkerPrints', 'items' => fn ($q) => $q->where('station', $station)->with('menu')])
            ->where('status', 'active')
            ->whereHas('items', fn ($q) => $q->where('station', $station))
            ->oldest()
            ->get();

        // partition(): grup pertama = yang lolos callback (sudah dicetak).
        [$printed, $unprinted] = $orders->partition(
            fn ($order) => $order->checkerPrints->contains('station', $station)
        );

        // BR-015: VIP dulu, sesama kelompok tetap FIFO (created_at menaik).
        $unprinted = $unprinted->sort(fn ($a, $b) =>
            [$b->table->is_vip, $a->created_at] <=> [$a->table->is_vip, $b->created_at]
        )->values();

        return ['unprinted' => $unprinted, 'printed' => $printed->values()];
    }

    /** Baseline deteksi item baru untuk polling. */
    protected function lastItemId(): int
    {
        return (int) OrderItem::where('station', $this->station())->max('id');
    }

    /**
     * Sidik jari komposisi antrian: berubah bila ada order masuk/keluar,
     * berpindah kelompok (dicetak/dibayar), atau urutan berubah.
     */
    protected function signature($unprinted, $printed): string
    {
        return md5(json_encode([
            $unprinted->pluck('id')->all(),
            $printed->pluck('id')->all(),
        ]));
    }
}
