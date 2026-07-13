<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaiterController extends Controller
{
    /** Dashboard Waiters (FR-005): pilih meja → nama pelanggan → menu → cart → kirim. */
    public function index(): View
    {
        $tables     = Table::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        // BR-002: hanya menu tersedia yang bisa dipilih — yang habis tidak dirender.
        // Payload ringan untuk Alpine (filter kategori, search, dan cart di client).
        $menuData = Menu::with('category')
            ->where('is_available', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($menu) => [
                'id'          => $menu->id,
                'name'        => $menu->name,
                'price'       => (int) $menu->price,
                'image'       => $menu->image ? asset('storage/' . $menu->image) : null,
                'category_id' => $menu->category_id,
            ])
            ->values();

        // Strip "Pesanan Aktif" (FR-013): order aktif terbaru, read-only.
        $activeOrders = Order::with(['table', 'items'])
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        $activeOrdersTotal = Order::where('status', 'active')->count();

        return view('waiter.dashboard', compact('tables', 'categories', 'menuData', 'activeOrders', 'activeOrdersTotal'));
    }

    /** Menu (Stok) — FR-012: Waiters melihat semua menu + toggle tersedia/habis. */
    public function menus(Request $request): View
    {
        $search   = trim((string) $request->input('search'));
        $category = $request->input('category');

        $menus = Menu::with('category')
            ->when($search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->when($category, fn ($query, $category) => $query->where('category_id', $category))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total'     => Menu::count(),
            'available' => Menu::where('is_available', true)->count(),
            'out'       => Menu::where('is_available', false)->count(),
        ];

        $categories = Category::orderBy('name')->get();

        return view('waiter.menus', compact('menus', 'search', 'category', 'stats', 'categories'));
    }

    /** FR-012: toggle tersedia ⇄ habis. Hanya status — CRUD menu tetap milik Admin (FR-003). */
    public function toggleAvailability(Menu $menu)
    {
        $menu->update(['is_available' => ! $menu->is_available]);

        return back()->with(
            'success',
            $menu->is_available
                ? "Menu \"{$menu->name}\" ditandai tersedia."
                : "Menu \"{$menu->name}\" ditandai habis."
        );
    }

    /** FR-013: daftar order aktif — murni read-only (BR-009/BR-010, tanpa tombol ubah status). */
    public function orders(): View
    {
        $orders = Order::with(['table', 'items'])
            ->where('status', 'active')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('waiter.orders', compact('orders'));
    }
}
