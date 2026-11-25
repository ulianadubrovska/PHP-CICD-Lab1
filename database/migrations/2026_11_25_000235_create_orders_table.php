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

            // Покупець, який створив замовлення
            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            // Дата замовлення
            $table->timestamp('ordered_at')->useCurrent();

            // Статус замовлення
            $table->string('status')->default('pending');

            // Загальна сума замовлення
            $table->decimal('total_amount', 10, 2)->default(0);

            // Коментар покупця
            $table->text('comment')->nullable();

            // Дата створення та оновлення
            $table->timestamps();

            // Індекс для пошуку замовлень
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
