<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function productData(Category $category): array
    {
        return [
            'name' => 'Футболка Lab8',
            'sku' => 'LAB8-API-001',
            'category_id' => $category->id,
            'description' => 'Тестовий товар',
            'price' => 899,
            'stock_quantity' => 10,
            'is_active' => true,
        ];
    }

    public function test_guest_can_view_products(): void
    {
        Product::factory()->create();

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_guest_cannot_create_product(): void
    {
        $category = Category::factory()->create();

        $this->postJson(
            '/api/v1/products',
            $this->productData($category)
        )->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_product(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson(
                '/api/v1/products',
                $this->productData($category)
            )
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id);

        $this->assertDatabaseHas('products', [
            'sku' => 'LAB8-API-001',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_update_another_users_product(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = Product::factory()->create();

        $product->user()->associate($owner);
        $product->save();

        $token = $otherUser->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->putJson(
                "/api/v1/products/{$product->id}",
                $this->productData($product->category)
            )
            ->assertForbidden();
    }

    public function test_user_cannot_delete_another_users_product(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = Product::factory()->create();

        $product->user()->associate($owner);
        $product->save();

        $token = $otherUser->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->deleteJson("/api/v1/products/{$product->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }

    public function test_invalid_product_data_returns_422(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/products', [
                'name' => 'A',
                'sku' => '',
                'price' => -100,
                'stock_quantity' => -1,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'sku',
                'category_id',
                'price',
                'stock_quantity',
            ]);
    }
}
