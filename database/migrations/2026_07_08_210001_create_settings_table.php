<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive — penyempurnaan Modul 8 (disetujui user).
 * Key-value settings: satu sumber data konfigurasi (BR-016 pajak dinamis).
 * Scalable untuk Modul Settings penuh nanti — setting baru = key baru, tanpa migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('value', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
