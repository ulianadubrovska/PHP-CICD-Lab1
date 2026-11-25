<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_payment_for_order(): void
    {
        $order = Order::factory()->create();

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => $order->total_amount,
        ]);

        $this->getJson("/payments?order_id={$order->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $payment->id]);
    }

    public function test_can_fetch_delivery_for_order(): void
    {
        $order = Order::factory()->create();

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
        ]);

        $this->getJson("/deliveries/{$delivery->id}")
            ->assertOk()
            ->assertJsonPath('data.order_id', $order->id);
    }

    public function test_can_create_review(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->withHeader(
            'X-Store-Client',
            'clothing-store-demo'
        )->postJson('/reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Чудова якість товару',
        ])->assertCreated();

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
        ]);
    }

    public function test_cannot_create_duplicate_review(): void
    {
        $review = Review::factory()->create();

        $this->withHeader(
            'X-Store-Client',
            'clothing-store-demo'
        )->postJson('/reviews', [
            'user_id' => $review->user_id,
            'product_id' => $review->product_id,
            'rating' => 4,
        ])->assertUnprocessable();

        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_invalid_review_rating_returns_422(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->withHeader(
            'X-Store-Client',
            'clothing-store-demo'
        )->postJson('/reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 7,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['rating']);
    }

    public function test_review_without_header_is_forbidden(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->postJson('/reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
        ])->assertForbidden();

        $this->assertDatabaseCount('reviews', 0);
    }
}
