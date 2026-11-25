<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAccessTest extends TestCase
{
    use RefreshDatabase;

    private function productData(int $categoryId): array
    {
        return [
            'category_id' => $categoryId,
            'name' => 'Сукня Midi',
            'sku' => 'DRS-TEST-001',
            'price' => 2300,
            'stock_quantity' => 5,
            'is_active' => true,
        ];
    }

    public function test_catalog_returns_200(): void
    {
        Product::factory()->create(['is_active' => true]);

        $this->getJson('/catalog')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data.data');
    }

    public function test_existing_product_returns_200(): void
    {
        $product = Product::factory()->create();

        $this->getJson("/catalog/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_missing_product_returns_404(): void
    {
        $this->getJson('/catalog/999999')
            ->assertNotFound();
    }

    public function test_post_without_client_header_returns_403(): void
    {
        $category = Category::factory()->create();

        $this->postJson(
            '/catalog',
            $this->productData($category->id)
        )->assertForbidden();

        $this->assertDatabaseMissing('products', [
            'sku' => 'DRS-TEST-001',
        ]);
    }

    public function test_post_with_valid_client_header_creates_product(): void
    {
        $category = Category::factory()->create();

        $this->withHeader(
            'X-Store-Client',
            'clothing-store-demo'
        )
            ->postJson(
                '/catalog',
                $this->productData($category->id)
            )
            ->assertCreated()
            ->assertHeader('X-Security-Policy', 'Enforced')
            ->assertJsonPath('data.name', 'Сукня Midi');

        $this->assertDatabaseHas('products', [
            'sku' => 'DRS-TEST-001',
            'category_id' => $category->id,
        ]);
    }

    public function test_invalid_product_data_returns_422(): void
    {
        $category = Category::factory()->create();

        $data = $this->productData($category->id);
        $data['price'] = -100;

        $this->withHeader(
            'X-Store-Client',
            'clothing-store-demo'
        )
            ->postJson('/catalog', $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['price']);
    }

    public function test_cart_summary_returns_correct_total(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $shirt = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 1200,
        ]);

        $belt = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 700,
        ]);

        $cart = $user->cart()->create();

        $cart->products()->attach($shirt->id, [
            'quantity' => 2,
        ]);

        $cart->products()->attach($belt->id, [
            'quantity' => 1,
        ]);

        $this->getJson(
            "/cart/summary?user_id={$user->id}"
        )
            ->assertOk()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.total', '3100.00')
            ->assertJsonCount(2, 'data.items');
    }
}
