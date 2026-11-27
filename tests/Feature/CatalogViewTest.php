<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
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
            ->assertSeeText($product->category->name)
            ->assertDontSeeText('Додати товар');
    }

    public function test_create_form_displays_categories(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'name' => 'Жіночий одяг',
        ]);

        $this->actingAs($user)
            ->get('/shop/catalog/create')
            ->assertOk()
            ->assertSeeText('Додати новий товар')
            ->assertSeeText($category->name);
    }

    public function test_can_create_product_using_html_form(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)
            ->post('/shop/catalog', [
                'name' => 'Жіноча футболка Oversize',
                'sku' => 'FSH-LAB8-TEST',
                'category_id' => $category->id,
                'description' => 'Тестовий товар',
                'price' => 899,
                'stock_quantity' => 15,
                'is_active' => 1,
            ])
            ->assertRedirect(route('shop.catalog.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'sku' => 'FSH-LAB8-TEST',
            'user_id' => $user->id,
        ]);
    }

    public function test_invalid_form_displays_validation_errors(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)
            ->post('/shop/catalog', [
                'name' => 'A',
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
    }

    public function test_can_update_own_product(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'name' => 'Стара назва',
            'price' => 899,
        ]);

        $product->user()->associate($user);
        $product->save();

        $this->actingAs($user)
            ->get("/shop/catalog/{$product->id}/edit")
            ->assertOk()
            ->assertSeeText('Редагування товару');

        $this->actingAs($user)
            ->put("/shop/catalog/{$product->id}", [
                'name' => 'Оновлена футболка',
                'sku' => $product->sku,
                'category_id' => $product->category_id,
                'description' => 'Оновлений опис',
                'price' => 999,
                'stock_quantity' => 10,
                'is_active' => 1,
            ])
            ->assertRedirect(route('shop.catalog.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Оновлена футболка',
            'user_id' => $user->id,
        ]);
    }

    public function test_can_delete_own_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $product->user()->associate($user);
        $product->save();

        $this->actingAs($user)
            ->delete("/shop/catalog/{$product->id}")
            ->assertRedirect(route('shop.catalog.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_guest_cannot_create_product(): void
    {
        $this->get('/shop/catalog/create')
            ->assertRedirect(route('login'));
    }

    public function test_user_cannot_edit_another_users_product(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $product = Product::factory()->create();

        $product->user()->associate($owner);
        $product->save();

        $this->actingAs($otherUser)
            ->get("/shop/catalog/{$product->id}/edit")
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->put("/shop/catalog/{$product->id}", [
                'name' => 'Чужа зміна',
                'sku' => $product->sku,
                'category_id' => $product->category_id,
                'price' => 100,
                'stock_quantity' => 5,
                'is_active' => 1,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name,
        ]);
    }

    public function test_user_cannot_delete_another_users_product(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $product = Product::factory()->create();

        $product->user()->associate($owner);
        $product->save();

        $this->actingAs($otherUser)
            ->delete("/shop/catalog/{$product->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }
}
