<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_order_with_products(): void
    {
        $user = User::factory()->create();

        $shirt = Product::factory()->create([
            'price' => 1200,
            'is_active' => true,
        ]);

        $belt = Product::factory()->create([
            'price' => 700,
            'is_active' => true,
        ]);

        $response = $this
            ->withHeader('X-Store-Client', 'clothing-store-demo')
            ->postJson('/orders', [
                'user_id' => $user->id,
                'items' => [
                    [
                        'product_id' => $shirt->id,
                        'quantity' => 2,
                    ],
                    [
                        'product_id' => $belt->id,
                        'quantity' => 1,
                    ],
                ],
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.total_amount', '3100.00');

        $orderId = $response->json('data.id');

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'user_id' => $user->id,
            'total_amount' => 3100,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $shirt->id,
            'quantity' => 2,
            'unit_price' => 1200,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $belt->id,
            'quantity' => 1,
            'unit_price' => 700,
        ]);
    }

    public function test_order_without_client_header_is_forbidden(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->postJson('/orders', [
            'user_id' => $user->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ])->assertForbidden();

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_can_fetch_order_with_relations(): void
    {
        $order = Order::factory()->create();

        $this->getJson("/orders/{$order->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonPath('data.user.id', $order->user_id);
    }
}
