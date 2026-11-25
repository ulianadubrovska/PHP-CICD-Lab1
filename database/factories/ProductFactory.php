<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'sku' => fake()->unique()->regexify('PRD-[0-9]{6}'),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 100, 5000),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'is_active' => fake()->boolean(85),
        ];
    }
}
