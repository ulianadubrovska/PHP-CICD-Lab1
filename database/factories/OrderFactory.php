<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'ordered_at' => now(),
            'status' => 'pending',
            'total_amount' => 0,
            'comment' => fake()->optional()->sentence(),
        ];
    }
}
