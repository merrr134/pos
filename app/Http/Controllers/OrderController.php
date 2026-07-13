<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Simpan order Waiters (FR-005).
     *
     * Seluruh proses dibungkus satu transaksi:
     * validasi meja (BR-001/BR-012) → validasi menu (BR-002) → generate order_number
     * → simpan order + items (snapshot harga & station, BR-003) → meja jadi terisi.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data) {
            // Lock baris meja agar dua submit bersamaan tidak lolos dua-duanya.
            $table = Table::whereKey($data['table_id'])->lockForUpdate()->firstOrFail();

            // BR-001 / BR-012: meja harus kosong dan belum punya order aktif.
            if ($table->status !== 'kosong' || $table->orders()->where('status', 'active')->exists()) {
                throw ValidationException::withMessages([
                    'table_id' => "{$table->name} sudah terisi dan tidak bisa menerima order baru.",
                ]);
            }

            $menus = Menu::with('category')
                ->whereIn('id', collect($data['items'])->pluck('menu_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // BR-002: re-validasi ketersediaan — menu bisa berubah habis antara render & submit.
            $unavailable = $menus->reject(fn ($menu) => $menu->is_available)->pluck('name');
            if ($unavailable->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'Menu berikut sudah habis: ' . $unavailable->join(', ') . '. Hapus dari order lalu kirim ulang.',
                ]);
            }

            // Nomor order harian: ORD-YYYYMMDD-#### (counter reset per hari).
            // Digenerate di dalam transaksi + unique index sebagai pengaman race condition.
            $prefix = 'ORD-' . now()->format('Ymd') . '-';
            $last   = Order::where('order_number', 'like', "{$prefix}%")->lockForUpdate()->max('order_number');
            $next   = $last ? ((int) substr($last, -4)) + 1 : 1;

            // Snapshot harga & station dihitung DI SERVER dari DB — input client tidak dipercaya.
            $total        = 0;
            $itemsPayload = [];
            foreach ($data['items'] as $row) {
                $menu     = $menus[$row['menu_id']];
                $subtotal = $menu->price * $row['qty'];
                $total   += $subtotal;

                $itemsPayload[] = [
                    'menu_id'  => $menu->id,
                    'qty'      => $row['qty'],
                    'price'    => $menu->price,              // snapshot harga saat order
                    'subtotal' => $subtotal,                 // qty × price
                    'station'  => $menu->category->station,  // BR-003: disalin dari kategori
                ];
            }

            $order = Order::create([
                'order_number'  => $prefix . str_pad($next, 4, '0', STR_PAD_LEFT),
                'table_id'      => $table->id,
                'customer_name' => $data['customer_name'],
                'total'         => $total,
                'status'        => 'active',
                'created_by'    => auth()->id(),
            ]);

            $order->items()->createMany($itemsPayload);

            // FR-004: setelah order dibuat, meja menjadi TERISI.
            $table->update(['status' => 'terisi']);

            return $order;
        });

        return redirect()
            ->route('waiter.dashboard')
            ->with('success', "Order {$order->order_number} untuk {$order->customer_name} berhasil dikirim ke station.");
    }
}
