<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique(); // ORD-YYYYMMDD-#### (bukan id db)
            $table->foreignId('table_id')->constrained('tables')->restrictOnDelete();
            $table->string('customer_name', 100);         // wajib (FR-005)
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('status', ['active', 'paid'])->default('active');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('status'); // filter order aktif (FR-013, kasir)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
