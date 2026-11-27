<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_categories_page_displays_categories(): void
    {
        Category::factory()->create([
            'name' => 'Жіночий одяг',
            'slug' => 'womens-clothing',
        ]);

        $this->get('/shop/categories')
            ->assertOk()
            ->assertSeeText('Жіночий одяг');
    }

    public function test_create_form_is_displayed(): void
    {
        $this->get('/shop/categories/create')
            ->assertOk()
            ->assertSeeText('Додати категорію');
    }

    public function test_can_create_category(): void
    {
        $this->post('/shop/categories', [
            'name' => 'Аксесуари для волосся',
            'slug' => 'hair-accessories',
            'description' => 'Прикраси для волосся',
        ])
            ->assertRedirect(route('shop.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Аксесуари для волосся',
            'slug' => 'hair-accessories',
        ]);
    }

    public function test_duplicate_slug_is_rejected(): void
    {
        Category::factory()->create([
            'slug' => 'hair-accessories',
        ]);

        $this->post('/shop/categories', [
            'name' => 'Нова категорія',
            'slug' => 'hair-accessories',
        ])
            ->assertSessionHasErrors('slug');

        $this->assertDatabaseCount('categories', 1);
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create();

        $this->put("/shop/categories/{$category->id}", [
            'name' => 'Оновлена категорія',
            'slug' => $category->slug,
            'description' => 'Новий опис',
        ])
            ->assertRedirect(route('shop.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Оновлена категорія',
        ]);
    }

    public function test_can_delete_empty_category(): void
    {
        $category = Category::factory()->create();

        $this->delete("/shop/categories/{$category->id}")
            ->assertRedirect(route('shop.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_cannot_delete_category_with_products(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->delete("/shop/categories/{$category->id}")
            ->assertRedirect(route('shop.categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }
}
