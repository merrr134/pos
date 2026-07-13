<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * FR-010 — Dashboard Admin, READ-ONLY, data asli.
     * Semua angka dari payments ber-paid_at (BR-005) sehingga sudah termasuk pajak:
     * pendapatan = SUM(amount_paid - change) = grand total (BR-016);
     * pajak = SUM(tax_amount) snapshot. Order belum dibayar TIDAK pernah dihitung.
     */
    public function index(): View
    {
        $startOfMonth = now()->startOfMonth();

        // ---- 1 query agregat per periode (pendapatan + pajak + jumlah order) ----
        $aggregate = fn ($query) => $query->selectRaw(
            'COALESCE(SUM(amount_paid - `change`), 0) as revenue,
             COALESCE(SUM(tax_amount), 0) as tax,
             COUNT(*) as orders'
        )->first();

        $today = $aggregate(Payment::whereDate('paid_at', today()));
        $month = $aggregate(Payment::where('paid_at', '>=', $startOfMonth));

        // ---- Cash vs QRIS BULAN INI (1 query) + persentase dihitung PHP ----
        $byMethod = Payment::where('paid_at', '>=', $startOfMonth)
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

        // ---- Count master + order aktif (read-only) ----
        $counts = [
            'menus'         => Menu::count(),
            'tables'        => Table::count(),
            'users'         => User::count(),
            'active_orders' => Order::where('status', 'active')->count(),
        ];

        // ---- Menu terlaris: 1 query agregat + eager load nama (total 2 query) ----
        $topMenus = OrderItem::query()
            ->select('menu_id', DB::raw('SUM(qty) as total_qty'))
            ->whereHas('order', fn ($q) => $q->where('status', 'paid'))
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('menu:id,name')
            ->get();

        // ---- Grafik 7 hari terakhir (1 query GROUP BY tanggal; hari kosong diisi 0) ----
        $rows = Payment::where('paid_at', '>=', today()->subDays(6)->startOfDay())
            ->selectRaw('DATE(paid_at) as day, COALESCE(SUM(amount_paid - `change`), 0) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $chart = ['labels' => [], 'data' => []];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $chart['labels'][] = $date->format('d/m');
            $chart['data'][]   = (float) ($rows[$date->toDateString()] ?? 0);
        }

        // ---- Aktivitas terbaru: 5 pembayaran terakhir (eager load — tanpa N+1) ----
        $recentPayments = Payment::with('order.table')
            ->latest('paid_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'today', 'month', 'methods', 'counts', 'topMenus', 'chart', 'recentPayments'
        ));
    }
}
