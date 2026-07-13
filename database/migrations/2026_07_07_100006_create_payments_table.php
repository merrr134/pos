<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete(); // BR-011
            $table->decimal('amount_paid', 12, 2);   // >= orders.total (BR-007)
            $table->decimal('change', 12, 2)->default(0);
            $table->enum('payment_method', ['cash', 'qris'])->default('cash');
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('paid_at'); // penanda transaksi selesai (BR-005)
            $table->timestamps();

            $table->index('paid_at'); // dashboard & laporan (FR-010/FR-011)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
