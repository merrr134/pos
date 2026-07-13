<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /** Daftar kategori + jumlah produk per kategori (FR-003). */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $categories = Category::query()
            ->withCount('menus')
            ->when($search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'categories' => Category::count(),
            'menus'      => Menu::count(),
        ];

        return view('admin.categories.index', compact('categories', 'search', 'stats'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Data kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // FK menus.category_id = RESTRICT — kategori yang masih punya menu tidak bisa dihapus.
        if ($category->menus()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki menu. Pindahkan atau hapus menunya terlebih dahulu.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
