<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_returns_products_with_categories(): void
    {
        $category = Category::factory()->create([
            'name' => 'Жіночий одяг',
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Футболка Oversize',
            'is_active' => true,
        ]);

        $this->getJson('/catalog')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Футболка Oversize'])
            ->assertJsonFragment(['name' => 'Жіночий одяг']);
    }

    public function test_product_can_be_saved_to_database(): void
    {
        $category = Category::factory()->create();

        $response = $this
            ->withHeader('X-Store-Client', 'clothing-store-demo')
            ->postJson('/catalog', [
                'category_id' => $category->id,
                'name' => 'Сукня Midi',
                'sku' => 'DRS-LAB6-100',
                'price' => 2300,
                'stock_quantity' => 5,
                'is_active' => true,
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('products', [
            'name' => 'Сукня Midi',
            'sku' => 'DRS-LAB6-100',
            'category_id' => $category->id,
        ]);
    }

    public function test_product_creation_without_client_header_is_forbidden(): void
    {
        $category = Category::factory()->create();

        $this->postJson('/catalog', [
            'category_id' => $category->id,
            'name' => 'Сукня Midi',
            'sku' => 'DRS-LAB6-101',
            'price' => 2300,
            'stock_quantity' => 5,
        ])->assertForbidden();

        $this->assertDatabaseMissing('products', [
            'sku' => 'DRS-LAB6-101',
        ]);
    }
}
