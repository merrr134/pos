<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->decimal('price', 12, 2);
            $table->boolean('is_available')->default(true); // BR-002, FR-012
            $table->timestamps();

            $table->index('is_available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
