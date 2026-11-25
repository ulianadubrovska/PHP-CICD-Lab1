<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // Покупець, який залишив відгук
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Товар, до якого належить відгук
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Оцінка товару від 1 до 5
            $table->unsignedTinyInteger('rating');

            // Текст відгуку
            $table->text('comment')->nullable();

            // Дата створення та оновлення
            $table->timestamps();

            // Один покупець може залишити один відгук на товар
            $table->unique(['user_id', 'product_id']);

            // Індекс для пошуку відгуків за товаром та оцінкою
            $table->index(['product_id', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
