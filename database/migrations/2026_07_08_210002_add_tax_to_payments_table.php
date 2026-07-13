<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive — penyempurnaan Modul 8 (disetujui user).
 * SNAPSHOT pajak saat pembayaran (BR-016): struk lama & laporan historis TIDAK berubah
 * ketika Admin mengubah persentase pajak. Default 0 = backward compatible dengan
 * pembayaran yang sudah ada sebelum fitur pajak.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('tax_percent', 5, 2)->default(0)->after('change');
            $table->decimal('tax_amount', 12, 2)->default(0)->after('tax_percent');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['tax_percent', 'tax_amount']);
        });
    }
};
