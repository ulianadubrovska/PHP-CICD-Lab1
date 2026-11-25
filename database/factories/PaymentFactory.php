<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'payment_method' => fake()->randomElement([
                'card',
                'cash_on_delivery',
            ]),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'status' => 'pending',
            'paid_at' => null,
        ];
    }
}
