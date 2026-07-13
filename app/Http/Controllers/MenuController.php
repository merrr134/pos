<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /** Daftar menu + filter kategori + search + statistik (FR-003). */
    public function index(Request $request)
    {
        $search   = trim((string) $request->input('search'));
        $category = $request->input('category'); // '' | id kategori

        $menus = Menu::query()
            ->with('category')
            ->withExists('orderItems') // kunci tombol hapus di UI tanpa query per-baris
            ->when($search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->when($category, fn ($query, $category) => $query->where('category_id', $category))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total'     => Menu::count(),
            'available' => Menu::where('is_available', true)->count(),
            'out'       => Menu::where('is_available', false)->count(),
        ];

        $categories = Category::orderBy('name')->get();

        return view('admin.menus.index', compact('menus', 'search', 'category', 'stats', 'categories'));
    }

    public function create()
    {
        return view('admin.menus.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(StoreMenuRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        Menu::create($data);

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu baru berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        return view('admin.menus.edit', [
            'menu'       => $menu,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Ganti gambar: hapus file lama agar storage tidak menumpuk.
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menus', 'public');
        } else {
            unset($data['image']); // tidak upload = gambar lama dipertahankan
        }

        $menu->update($data);

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Data menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        // FK order_items.menu_id = RESTRICT — menu yang punya riwayat order tidak bisa dihapus.
        if ($menu->orderItems()->exists()) {
            return back()->with('error', 'Menu tidak bisa dihapus karena sudah memiliki riwayat order. Nonaktifkan saja statusnya.');
        }

        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }

    /** Toggle tersedia/habis (FR-003; dipakai Admin — versi Waiters menyusul di FR-012). */
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
}
