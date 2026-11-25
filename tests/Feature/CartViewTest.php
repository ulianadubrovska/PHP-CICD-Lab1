<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_carts_page_displays_carts(): void
    {
        $cart = Cart::factory()->create();

        $this->get('/shop/carts')
            ->assertOk()
            ->assertSeeText('Кошики покупців')
            ->assertSeeText($cart->user->name);
    }

    public function test_can_view_cart_with_products(): void
    {
        $cart = Cart::factory()->create();

        $product = Product::factory()->create([
            'name' => 'Жіноча футболка',
            'price' => 500,
        ]);

        $cart->cartItems()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->get("/shop/carts/{$cart->id}")
            ->assertOk()
            ->assertSeeText('Жіноча футболка')
            ->assertSeeText('1 000,00');
    }

    public function test_can_add_product_to_cart(): void
    {
        $cart = Cart::factory()->create();

        $product = Product::factory()->create([
            'stock_quantity' => 20,
            'is_active' => true,
        ]);

        $this->post("/shop/carts/{$cart->id}/items", [
            'product_id' => $product->id,
            'quantity' => 3,
        ])
            ->assertRedirect(route('shop.carts.show', $cart->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_can_update_product_quantity(): void
    {
        $cart = Cart::factory()->create();

        $product = Product::factory()->create([
            'stock_quantity' => 20,
        ]);

        $item = $cart->cartItems()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->put("/shop/carts/{$cart->id}/items/{$item->id}", [
            'quantity' => 5,
        ])
            ->assertRedirect(route('shop.carts.show', $cart->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 5,
        ]);
    }

    public function test_can_remove_product_from_cart(): void
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create();

        $item = $cart->cartItems()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->delete("/shop/carts/{$cart->id}/items/{$item->id}")
            ->assertRedirect(route('shop.carts.show', $cart->id))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('cart_items', [
            'id' => $item->id,
        ]);
    }

    public function test_cannot_add_more_than_stock_quantity(): void
    {
        $cart = Cart::factory()->create();

        $product = Product::factory()->create([
            'stock_quantity' => 2,
            'is_active' => true,
        ]);

        $this->post("/shop/carts/{$cart->id}/items", [
            'product_id' => $product->id,
            'quantity' => 5,
        ])->assertSessionHasErrors('quantity');

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_cannot_change_item_from_another_cart(): void
    {
        $firstCart = Cart::factory()->create();
        $secondCart = Cart::factory()->create();

        $product = Product::factory()->create([
            'stock_quantity' => 20,
        ]);

        $item = $secondCart->cartItems()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->put("/shop/carts/{$firstCart->id}/items/{$item->id}", [
            'quantity' => 5,
        ])->assertNotFound();

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 2,
        ]);
    }
}
