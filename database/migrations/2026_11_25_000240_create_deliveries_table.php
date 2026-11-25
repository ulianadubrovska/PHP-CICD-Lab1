<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();

            // Замовлення, для якого оформлено доставку
            $table->foreignId('order_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            // Спосіб доставки
            $table->string('delivery_method');

            // Дані отримувача
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);

            // Адреса доставки
            $table->string('city');
            $table->string('address');
            $table->string('postal_code', 20)->nullable();

            // Статус доставки
            $table->string('status')->default('pending');

            // Дата фактичного отримання
            $table->timestamp('delivered_at')->nullable();

            // Дата створення та оновлення
            $table->timestamps();

            // Індекс для пошуку доставок за статусом
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
