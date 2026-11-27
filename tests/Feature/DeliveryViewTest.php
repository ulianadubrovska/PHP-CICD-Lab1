<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    private function deliveryData(int $orderId): array
    {
        return [
            'order_id' => $orderId,
            'delivery_method' => 'nova_poshta',
            'recipient_name' => 'Тестовий Покупець',
            'recipient_phone' => '+380000000000',
            'city' => 'Івано-Франківськ',
            'address' => 'Тестове відділення №1',
            'postal_code' => '76000',
            'status' => 'pending',
        ];
    }

    public function test_deliveries_page_displays_deliveries(): void
    {
        $delivery = Delivery::factory()->create([
            'city' => 'Івано-Франківськ',
        ]);

        $this->get('/shop/deliveries')
            ->assertOk()
            ->assertSeeText('Доставки замовлень')
            ->assertSeeText('Івано-Франківськ')
            ->assertSeeText($delivery->recipient_name);
    }

    public function test_create_form_displays_orders(): void
    {
        $order = Order::factory()->create();

        $this->get('/shop/deliveries/create')
            ->assertOk()
            ->assertSeeText('Додати доставку')
            ->assertSeeText('№'.$order->id);
    }

    public function test_can_create_delivery(): void
    {
        $order = Order::factory()->create();

        $this->post(
            '/shop/deliveries',
            $this->deliveryData($order->id)
        )
            ->assertRedirect(route('shop.deliveries.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('deliveries', [
            'order_id' => $order->id,
            'city' => 'Івано-Франківськ',
            'status' => 'pending',
        ]);
    }

    public function test_cannot_create_two_deliveries_for_same_order(): void
    {
        $delivery = Delivery::factory()->create();

        $this->post(
            '/shop/deliveries',
            $this->deliveryData($delivery->order_id)
        )->assertSessionHasErrors('order_id');

        $this->assertDatabaseCount('deliveries', 1);
    }

    public function test_invalid_delivery_data_is_rejected(): void
    {
        $order = Order::factory()->create();

        $data = $this->deliveryData($order->id);
        $data['recipient_name'] = '';
        $data['city'] = '';
        $data['status'] = 'unknown';

        $this->post('/shop/deliveries', $data)
            ->assertSessionHasErrors([
                'recipient_name',
                'city',
                'status',
            ]);

        $this->assertDatabaseCount('deliveries', 0);
    }

    public function test_can_update_delivery_status(): void
    {
        $delivery = Delivery::factory()->create([
            'status' => 'pending',
            'delivered_at' => null,
        ]);

        $data = $this->deliveryData($delivery->order_id);
        $data['status'] = 'delivered';

        $this->put(
            "/shop/deliveries/{$delivery->id}",
            $data
        )
            ->assertRedirect(route('shop.deliveries.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => 'delivered',
        ]);

        $this->assertNotNull($delivery->fresh()->delivered_at);
    }

    public function test_can_delete_delivery(): void
    {
        $delivery = Delivery::factory()->create();

        $this->delete("/shop/deliveries/{$delivery->id}")
            ->assertRedirect(route('shop.deliveries.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('deliveries', [
            'id' => $delivery->id,
        ]);
    }
}
