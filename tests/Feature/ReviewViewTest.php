<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_reviews_page_displays_reviews(): void
    {
        $review = Review::factory()->create([
            'comment' => 'Чудова якість товару',
        ]);

        $this->get('/shop/reviews')
            ->assertOk()
            ->assertSeeText('Чудова якість товару')
            ->assertSeeText($review->user->name);
    }

    public function test_create_form_displays_users_and_products(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->get('/shop/reviews/create')
            ->assertOk()
            ->assertSeeText($user->name)
            ->assertSeeText($product->name);
    }

    public function test_can_create_review(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->post('/shop/reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Гарний товар',
        ])
            ->assertRedirect(route('shop.reviews.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Гарний товар',
        ]);
    }

    public function test_duplicate_review_is_rejected(): void
    {
        $review = Review::factory()->create();

        $this->post('/shop/reviews', [
            'user_id' => $review->user_id,
            'product_id' => $review->product_id,
            'rating' => 4,
        ])->assertSessionHasErrors('product_id');

        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_invalid_rating_is_rejected(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->post('/shop/reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 7,
        ])->assertSessionHasErrors('rating');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_can_update_review(): void
    {
        $review = Review::factory()->create();

        $this->put("/shop/reviews/{$review->id}", [
            'user_id' => $review->user_id,
            'product_id' => $review->product_id,
            'rating' => 4,
            'comment' => 'Оновлений коментар',
        ])
            ->assertRedirect(route('shop.reviews.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 4,
            'comment' => 'Оновлений коментар',
        ]);
    }

    public function test_can_delete_review(): void
    {
        $review = Review::factory()->create();

        $this->delete("/shop/reviews/{$review->id}")
            ->assertRedirect(route('shop.reviews.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }
}
