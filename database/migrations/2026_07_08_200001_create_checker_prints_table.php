<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive — penyempurnaan Modul 6 (disetujui user).
 * BR-014: checker hanya bisa dicetak SATU KALI per station per order.
 * UNIQUE(order_id, station) = penegakan atomik di level DB (kebal race condition).
 * station memakai varchar (bukan enum) agar station baru tidak butuh ALTER.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checker_prints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('station', 20); // 'kitchen' | 'barista' | (station masa depan)
            $table->timestamp('printed_at');
            $table->timestamps();

            $table->unique(['order_id', 'station']); // BR-014
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checker_prints');
    }
};
