<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_payments_page_displays_payments(): void
    {
        $payment = Payment::factory()->create();

        $this->get('/shop/payments')
            ->assertOk()
            ->assertSeeText('Оплати замовлень')
            ->assertSeeText('№'.$payment->order_id);
    }

    public function test_create_form_displays_orders(): void
    {
        $order = Order::factory()->create();

        $this->get('/shop/payments/create')
            ->assertOk()
            ->assertSeeText('Додати оплату')
            ->assertSeeText('№'.$order->id);
    }

    public function test_can_create_payment(): void
    {
        $order = Order::factory()->create();

        $this->post('/shop/payments', [
            'order_id' => $order->id,
            'payment_method' => 'card',
            'amount' => 500,
            'status' => 'pending',
        ])
            ->assertRedirect(route('shop.payments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'card',
            'amount' => 500,
            'status' => 'pending',
        ]);
    }

    public function test_cannot_create_second_payment_for_same_order(): void
    {
        $payment = Payment::factory()->create();

        $this->post('/shop/payments', [
            'order_id' => $payment->order_id,
            'payment_method' => 'card',
            'amount' => 500,
            'status' => 'pending',
        ])->assertSessionHasErrors('order_id');

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_invalid_payment_amount_is_rejected(): void
    {
        $order = Order::factory()->create();

        $this->post('/shop/payments', [
            'order_id' => $order->id,
            'payment_method' => 'card',
            'amount' => -100,
            'status' => 'pending',
        ])->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_can_update_payment_status(): void
    {
        $payment = Payment::factory()->create([
            'status' => 'pending',
            'paid_at' => null,
        ]);

        $this->put("/shop/payments/{$payment->id}", [
            'order_id' => $payment->order_id,
            'payment_method' => 'card',
            'amount' => $payment->amount,
            'status' => 'paid',
        ])
            ->assertRedirect(route('shop.payments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);

        $this->assertNotNull($payment->fresh()->paid_at);
    }

    public function test_can_delete_payment(): void
    {
        $payment = Payment::factory()->create();

        $this->delete("/shop/payments/{$payment->id}")
            ->assertRedirect(route('shop.payments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id,
        ]);
    }
}
