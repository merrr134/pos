<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * FR-011 — Laporan Penjualan Admin. READ-ONLY.
 * Semua nilai dari payments ber-paid_at (BR-005) + snapshot (BR-016):
 * pendapatan = SUM(amount_paid - change) = grand total; pajak = SUM(tax_amount).
 * Data halaman & export PDF dibangun oleh method yang SAMA agar selalu konsisten filter.
 */
class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $data = $this->reportData($request);

        // Tabel produk terlaris halaman: paginate(5) sesuai Figma.
        $data['topProducts'] = $this->topProductsQuery($data['from'], $data['to'])
            ->paginate(5)
            ->withQueryString();

        return view('admin.reports.index', $data);
    }

    /** Export PDF (DomPDF) — filter sama dengan halaman; grafik diganti tabel harian (keputusan user). */
    public function export(Request $request)
    {
        $data = $this->reportData($request);
        $data['topProducts'] = $this->topProductsQuery($data['from'], $data['to'])->get(); // lengkap, tanpa pagination

        $filename = 'laporan-penjualan-' . $data['from']->format('Ymd') . '-' . $data['to']->format('Ymd') . '.pdf';

        return Pdf::loadView('admin.reports.pdf', $data)->stream($filename);
    }

    /** Seluruh data laporan untuk satu rentang tanggal. */
    private function reportData(Request $request): array
    {
        // Default 7 hari terakhir (disetujui user); rentang terbalik dinormalkan.
        $from = $request->filled('date_from')
            ? Carbon::parse($request->input('date_from'))->startOfDay()
            : today()->subDays(6)->startOfDay();
        $to = $request->filled('date_to')
            ? Carbon::parse($request->input('date_to'))->endOfDay()
            : today()->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $range = fn () => Payment::whereBetween('paid_at', [$from, $to]);

        // ---- Ringkasan (1 query agregat) ----
        $stats = $range()->selectRaw(
            'COUNT(*) as orders,
             COALESCE(SUM(amount_paid - `change`), 0) as revenue,
             COALESCE(SUM(tax_amount), 0) as tax'
        )->first();

        $average = $stats->orders > 0 ? (float) $stats->revenue / $stats->orders : 0;

        // ---- Penjualan harian (1 query; hari kosong diisi 0) — dipakai chart & tabel PDF ----
        $rows = $range()
            ->selectRaw('DATE(paid_at) as day, COALESCE(SUM(amount_paid - `change`), 0) as total, COUNT(*) as orders')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $daily = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $daily[] = [
                'label'  => $d->format('d/m'),
                'total'  => (float) ($rows[$key]->total ?? 0),
                'orders' => (int) ($rows[$key]->orders ?? 0),
            ];
        }

        // SRS FR-011: batang PEAK di-highlight GOLD — warna dihitung server agar deterministik & testable.
        $max   = collect($daily)->max('total');
        $chart = [
            'labels' => array_column($daily, 'label'),
            'data'   => array_column($daily, 'total'),
            'colors' => array_map(
                fn ($row) => ($max > 0 && $row['total'] === $max) ? '#F59E0B' : '#7C4A2D', // gold | brand
                $daily
            ),
        ];

        // ---- Distribusi metode (1 query) ----
        $byMethod = $range()
            ->selectRaw('payment_method, COALESCE(SUM(amount_paid - `change`), 0) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        $cashTotal   = (float) ($byMethod['cash'] ?? 0);
        $qrisTotal   = (float) ($byMethod['qris'] ?? 0);
        $methodTotal = $cashTotal + $qrisTotal;

        $methods = [
            'cash'         => $cashTotal,
            'qris'         => $qrisTotal,
            'cash_percent' => $methodTotal > 0 ? round($cashTotal / $methodTotal * 100, 1) : 0,
            'qris_percent' => $methodTotal > 0 ? round($qrisTotal / $methodTotal * 100, 1) : 0,
        ];

        // Kartu #4: produk terlaris peringkat 1 pada rentang ini.
        $topProduct = $this->topProductsQuery($from, $to)->first();

        return compact('from', 'to', 'stats', 'average', 'daily', 'chart', 'methods', 'topProduct');
    }

    /** Produk terlaris pada rentang: hanya item dari order yang DIBAYAR dalam rentang (snapshot subtotal). */
    private function topProductsQuery(Carbon $from, Carbon $to)
    {
        return OrderItem::query()
            ->select('menu_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('order.payment', fn ($q) => $q->whereBetween('paid_at', [$from, $to]))
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->with('menu.category'); // eager — tanpa N+1
    }
}
