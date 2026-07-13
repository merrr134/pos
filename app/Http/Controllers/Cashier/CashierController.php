<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierController extends Controller
{
    /**
     * FR-008 — dashboard kasir: cari tagihan → pilih order aktif (?order={id},
     * render server-side) → panel pembayaran. Plus riwayat pembayaran hari ini.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $orders = Order::with(['table', 'items'])
            ->where('status', 'active')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhereHas('table', fn ($t) => $t->where('name', 'like', "%{$search}%"));
                });
            })
            ->oldest() // FIFO — tagihan tertua di depan
            ->get();

        // Order terpilih (BR-013: hanya order aktif yang bisa diproses).
        $selected = $request->filled('order')
            ? Order::with(['table', 'items.menu'])->where('status', 'active')->find($request->input('order'))
            : null;

        // Riwayat pembayaran hari ini (link cetak ulang struk — reprint diperbolehkan).
        $todayPayments = Payment::with('order.table')
            ->whereDate('paid_at', today())
            ->latest('paid_at')
            ->take(8)
            ->get();

        // Kartu sukses pasca-bayar (?paid={id}) dengan tombol Cetak Struk.
        $justPaid = $request->filled('paid')
            ? Order::with('table')->where('status', 'paid')->find($request->input('paid'))
            : null;

        // BR-016: kasir hanya MEMBACA pajak — nilainya diatur Admin via Settings.
        $taxPercent = Setting::taxPercent();

        return view('cashier.index', compact('orders', 'search', 'selected', 'todayPayments', 'justPaid', 'taxPercent'));
    }
}
