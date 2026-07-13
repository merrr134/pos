<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\View\View;

class SettingController extends Controller
{
    /** Halaman Pengaturan Admin — saat ini berisi pajak (BR-016); setting lain menyusul di Modul Settings. */
    public function edit(): View
    {
        return view('admin.settings.index', [
            'taxPercent' => Setting::taxPercent(),
        ]);
    }

    public function update(UpdateSettingsRequest $request)
    {
        // Satu sumber data: seluruh sistem (kasir, struk, laporan) membaca dari sini.
        Setting::put('tax_percent', (string) $request->validated()['tax_percent']);

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Pengaturan pajak berhasil disimpan.');
    }
}
