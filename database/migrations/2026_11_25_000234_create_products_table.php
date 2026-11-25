<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Зв'язок із категорією товару
            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            // Основна інформація про товар
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();

            // Ціна та кількість товару на складі
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock_quantity')->default(0);

            // Чи доступний товар у каталозі
            $table->boolean('is_active')->default(true);

            // Дата створення та оновлення
            $table->timestamps();

            // Індекс для оптимізації пошуку
            $table->index(['is_active', 'price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
