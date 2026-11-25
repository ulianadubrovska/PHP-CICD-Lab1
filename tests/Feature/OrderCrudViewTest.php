<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCrudViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_form_displays_order(): void
    {
        $order = Order::factory()->create();

        $product = Product::factory()->create([
            'price' => 500,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);

        $order->products()->attach($product->id, [
            'quantity' => 2,
            'unit_price' => 500,
        ]);

        $this->get("/shop/orders/{$order->id}/edit")
            ->assertOk()
            ->assertSeeText('Редагування замовлення')
            ->assertSeeText($product->name);
    }

    public function test_can_update_order_products_and_total(): void
    {
        $order = Order::factory()->create([
            'status' => 'pending',
        ]);

        $first = Product::factory()->create([
            'price' => 500,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);

        $second = Product::factory()->create([
            'price' => 300,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);

        $order->products()->attach($first->id, [
            'quantity' => 1,
            'unit_price' => 500,
        ]);

        $this->put("/shop/orders/{$order->id}", [
            'user_id' => $order->user_id,
            'products' => [$first->id, $second->id],
            'quantities' => [
                $first->id => 2,
                $second->id => 3,
            ],
        ])
            ->assertRedirect(route('shop.orders.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'total_amount' => 1900,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $first->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $second->id,
            'quantity' => 3,
        ]);

        $this->assertCount(2, $order->fresh()->products);
    }

    public function test_invalid_quantity_is_rejected(): void
    {
        $order = Order::factory()->create([
            'status' => 'pending',
        ]);

        $product = Product::factory()->create([
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $this->put("/shop/orders/{$order->id}", [
            'user_id' => $order->user_id,
            'products' => [$product->id],
            'quantities' => [
                $product->id => 0,
            ],
        ])->assertSessionHasErrors('quantities');

        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_can_delete_pending_order_without_payment(): void
    {
        $order = Order::factory()->create([
            'status' => 'pending',
        ]);

        $this->delete("/shop/orders/{$order->id}")
            ->assertRedirect(route('shop.orders.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }

    public function test_cannot_delete_order_with_payment(): void
    {
        $order = Order::factory()->create([
            'status' => 'pending',
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
        ]);

        $this->delete("/shop/orders/{$order->id}")
            ->assertRedirect(route('shop.orders.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
        ]);
    }
}
