<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductAccessTest extends TestCase
{
    public function test_catalog_returns_products(): void
    {
        $this->getJson('/catalog')
            ->assertOk()
            ->assertJsonPath('count', 3)
            ->assertJsonPath('data.0.name', 'Футболка Oversize');
    }

    public function test_existing_product_can_be_found(): void
    {
        $this->getJson('/catalog/1')
            ->assertOk()
            ->assertJsonPath('data.id', 1);
    }

    public function test_missing_product_returns_404(): void
    {
        $this->getJson('/catalog/999')
            ->assertNotFound();
    }

    public function test_post_without_client_header_is_forbidden(): void
    {
        $this->postJson('/catalog', $this->productData())
            ->assertForbidden()
            ->assertJsonPath('status', 'access_denied');
    }

    public function test_post_with_valid_client_header_creates_product(): void
    {
        $this->withHeader('X-Store-Client', 'clothing-store-demo')
            ->postJson('/catalog', $this->productData())
            ->assertCreated()
            ->assertHeader('X-Security-Policy', 'Enforced')
            ->assertJsonPath('data.name', 'Сукня Midi');
    }

    public function test_invalid_product_data_returns_422(): void
    {
        $data = $this->productData();
        $data['price'] = -100;

        $this->withHeader('X-Store-Client', 'clothing-store-demo')
            ->postJson('/catalog', $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['price']);
    }

    public function test_cart_summary_returns_correct_total(): void
    {
        $this->getJson('/cart/summary')
            ->assertOk()
            ->assertJsonPath('total', 3100)
            ->assertJsonPath('currency', 'UAH');
    }

    private function productData(): array
    {
        return [
            'name' => 'Сукня Midi',
            'description' => 'Жіноча сукня середньої довжини',
            'category_id' => 3,
            'price' => 2300,
            'stock' => 7,
        ];
    }
}
