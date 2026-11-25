<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            // Кошик, до якого належить товар
            $table->foreignId('cart_id')
                ->constrained()
                ->cascadeOnDelete();

            // Товар, доданий у кошик
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Кількість одиниць товару
            $table->unsignedInteger('quantity')->default(1);

            // Дата створення та оновлення
            $table->timestamps();

            // Один товар не повинен повторюватися в кошику
            $table->unique(['cart_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
