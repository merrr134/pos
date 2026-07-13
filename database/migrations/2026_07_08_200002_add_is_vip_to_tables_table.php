<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive — penyempurnaan Modul 6 (disetujui user).
 * BR-015: meja VIP diprioritaskan di antrian station (VIP dulu, lalu FIFO).
 * Nama mengikuti konvensi boolean skema: is_active, is_available.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->boolean('is_vip')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn('is_vip');
        });
    }
};
