<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->restrictOnDelete();
            $table->integer('qty');
            $table->decimal('price', 12, 2);    // snapshot harga saat order
            $table->decimal('subtotal', 12, 2); // qty x price
            $table->enum('station', ['kitchen', 'barista']); // disalin dari categories.station
            $table->timestamps();

            $table->index('station'); // antrian per station (FR-006/FR-007 polling)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
