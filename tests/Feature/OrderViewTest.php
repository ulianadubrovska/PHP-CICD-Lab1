<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_orders_page_displays_orders(): void
    {
        $order = Order::factory()->create();

        $this->get('/shop/orders')
            ->assertOk()
            ->assertSeeText('Замовлення')
            ->assertSeeText($order->user->name);
    }

    public function test_create_form_displays_products_and_users(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'is_active' => true,
        ]);

        $this->get('/shop/orders/create')
            ->assertOk()
            ->assertSeeText($user->name)
            ->assertSeeText($product->name);
    }

    public function test_can_create_order_with_two_products(): void
    {
        $user = User::factory()->create();

        $first = Product::factory()->create([
            'price' => 500,
            'is_active' => true,
        ]);

        $second = Product::factory()->create([
            'price' => 300,
            'is_active' => true,
        ]);

        $response = $this->post('/shop/orders', [
            'user_id' => $user->id,
            'products' => [$first->id, $second->id],
            'quantities' => [
                $first->id => 2,
                $second->id => 3,
            ],
        ]);

        $response
            ->assertRedirect(route('shop.orders.index'))
            ->assertSessionHas('success');

        $order = Order::where('user_id', $user->id)->firstOrFail();

        $this->assertEquals(1900, (float) $order->total_amount);

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

        $this->assertCount(2, $order->products);
    }

    public function test_order_requires_at_least_one_product(): void
    {
        $user = User::factory()->create();

        $this->post('/shop/orders', [
            'user_id' => $user->id,
            'products' => [],
            'quantities' => [],
        ])->assertSessionHasErrors('products');

        $this->assertDatabaseCount('orders', 0);
    }
}
