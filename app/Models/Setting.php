<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Key-value settings aplikasi — satu sumber data konfigurasi.
 * Dipakai pertama kali untuk pajak dinamis (BR-016); Modul Settings penuh
 * nanti tinggal menambah key baru tanpa migration.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function put(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** Persentase pajak aktif (BR-016) — SATU-SATUNYA sumber nilai pajak di seluruh sistem. */
    public static function taxPercent(): float
    {
        return (float) static::get('tax_percent', '0');
    }
}
