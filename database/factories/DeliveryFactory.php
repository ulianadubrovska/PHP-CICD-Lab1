<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'delivery_method' => 'nova_poshta',
            'recipient_name' => fake()->name(),
            'recipient_phone' => fake()->numerify('+380#########'),
            'city' => fake()->city(),
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'status' => 'pending',
            'delivered_at' => null,
        ];
    }
}
