<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_displays_products(): void
    {
        $product = Product::factory()->create([
            'name' => 'Жіноча футболка Oversize',
        ]);

        $this->get('/shop/catalog')
            ->assertOk()
            ->assertSeeText('Жіноча футболка Oversize')
            ->assertSeeText('Додати товар')
            ->assertSeeText($product->category->name);
    }

    public function test_create_form_displays_categories(): void
    {
        $category = Category::factory()->create([
            'name' => 'Жіночий одяг',
        ]);

        $this->get('/shop/catalog/create')
            ->assertOk()
            ->assertSeeText('Додати новий товар')
            ->assertSeeText('Жіночий одяг');
    }

    public function test_can_create_product_using_html_form(): void
    {
        $category = Category::factory()->create();

        $response = $this->post('/shop/catalog', [
            'name' => 'Жіноча футболка Oversize',
            'sku' => 'FSH-LAB7-TEST',
            'category_id' => $category->id,
            'description' => 'Базова футболка',
            'price' => 899.00,
            'stock_quantity' => 15,
            'is_active' => 1,
        ]);

        $response
            ->assertRedirect(route('shop.catalog.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'sku' => 'FSH-LAB7-TEST',
            'name' => 'Жіноча футболка Oversize',
            'category_id' => $category->id,
        ]);
    }

    public function test_invalid_form_displays_validation_errors(): void
    {
        $category = Category::factory()->create();

        $this->post('/shop/catalog', [
            'name' => '',
            'sku' => 'INVALID-TEST',
            'category_id' => $category->id,
            'price' => -100,
            'stock_quantity' => -5,
            'is_active' => 1,
        ])
            ->assertSessionHasErrors([
                'name',
                'price',
                'stock_quantity',
            ]);

        $this->assertDatabaseMissing('products', [
            'sku' => 'INVALID-TEST',
        ]);
    }

    public function test_can_update_product(): void
    {
        $product = Product::factory()->create([
            'price' => 899,
        ]);

        $this->get("/shop/catalog/{$product->id}/edit")
            ->assertOk()
            ->assertSeeText('Редагування товару');

        $this->put("/shop/catalog/{$product->id}", [
            'name' => 'Оновлена футболка',
            'sku' => $product->sku,
            'category_id' => $product->category_id,
            'description' => 'Оновлений опис',
            'price' => 999,
            'stock_quantity' => 15,
            'is_active' => 1,
        ])
            ->assertRedirect(route('shop.catalog.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Оновлена футболка',
            'price' => 999,
        ]);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $this->delete("/shop/catalog/{$product->id}")
            ->assertRedirect(route('shop.catalog.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
