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

            // Замовлення, до якого належить оплата
            $table->foreignId('order_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            // Спосіб оплати
            $table->string('payment_method');

            // Сума платежу
            $table->decimal('amount', 10, 2);

            // Статус оплати
            $table->string('status')->default('pending');

            // Дата здійснення оплати
            $table->timestamp('paid_at')->nullable();

            // Дата створення та оновлення
            $table->timestamps();

            // Індекс для пошуку оплат за статусом
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
