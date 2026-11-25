<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Створюємо 5 категорій та 40 товарів
        Category::factory()
            ->count(5)
            ->has(Product::factory()->count(8))
            ->create();

        $products = Product::all();

        // Створюємо 5 покупців
        $users = User::factory()->count(5)->create();

        foreach ($users as $user) {
            // Створюємо кошик покупця
            $cart = $user->cart()->create();

            foreach ($products->random(3) as $product) {
                $cart->products()->attach($product->id, [
                    'quantity' => fake()->numberBetween(1, 3),
                ]);
            }

            // Створюємо 2 замовлення для кожного покупця
            for ($i = 0; $i < 2; $i++) {
                $order = $user->orders()->create([
                    'ordered_at' => now(),
                    'status' => 'pending',
                    'total_amount' => 0,
                ]);

                $totalCents = 0;

                foreach ($products->random(2) as $product) {
                    $quantity = fake()->numberBetween(1, 3);
                    $unitPrice = $product->price;

                    $order->products()->attach($product->id, [
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                    ]);

                    $priceCents = (int) round(
                        (float) $unitPrice * 100
                    );

                    $totalCents += $priceCents * $quantity;
                }

                $order->update([
                    'total_amount' => $totalCents / 100,
                ]);

                // Оплата замовлення
                $order->payment()->create([
                    'payment_method' => 'card',
                    'amount' => $order->total_amount,
                    'status' => 'pending',
                ]);

                // Доставка замовлення
                $order->delivery()->create([
                    'delivery_method' => 'nova_poshta',
                    'recipient_name' => $user->name,
                    'recipient_phone' => fake()->numerify('+380#########'),
                    'city' => fake()->city(),
                    'address' => fake()->streetAddress(),
                    'postal_code' => fake()->postcode(),
                    'status' => 'pending',
                ]);
            }

            // Створюємо 2 відгуки від кожного покупця
            foreach ($products->random(2) as $product) {
                $user->reviews()->create([
                    'product_id' => $product->id,
                    'rating' => fake()->numberBetween(1, 5),
                    'comment' => fake()->sentence(),
                ]);
            }
        }
    }
}
