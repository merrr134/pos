<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /** Daftar meja + filter status + search + statistik (FR-004). */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status'); // '' | kosong | terisi

        $tables = Table::query()
            ->withExists('orders') // kunci tombol hapus di UI tanpa query per-kartu
            ->when($search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->when(in_array($status, ['kosong', 'terisi'], true), fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(8) // grid kartu (Figma: 8 meja per halaman)
            ->withQueryString();

        $stats = [
            'total'  => Table::count(),
            'terisi' => Table::where('status', 'terisi')->count(),
            'kosong' => Table::where('status', 'kosong')->count(),
        ];

        return view('admin.tables.index', compact('tables', 'search', 'status', 'stats'));
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(StoreTableRequest $request)
    {
        // Status TIDAK diisi dari input — meja baru selalu 'kosong' (default DB, FR-004).
        Table::create($request->validated());

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Meja baru berhasil ditambahkan.');
    }

    public function edit(Table $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function update(UpdateTableRequest $request, Table $table)
    {
        // Hanya nama yang bisa diubah admin — status milik sistem (FR-004).
        $table->update($request->validated());

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Data meja berhasil diperbarui.');
    }

    public function destroy(Table $table)
    {
        // Meja yang sedang dipakai tidak boleh dihapus.
        if ($table->status === 'terisi') {
            return back()->with('error', 'Meja sedang terisi dan tidak bisa dihapus. Selesaikan pembayarannya terlebih dahulu.');
        }

        // FK orders.table_id = RESTRICT — meja dengan riwayat order tidak bisa dihapus.
        if ($table->orders()->exists()) {
            return back()->with('error', 'Meja tidak bisa dihapus karena sudah memiliki riwayat order.');
        }

        $table->delete();

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Meja berhasil dihapus.');
    }
}
