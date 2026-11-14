<?php

declare(strict_types=1);

namespace App\Tasks;

class Store
{
    private array $products = [];

    public function __construct(
        private int $storeId,
        private string $name
    ) {}

    public function addProduct(Product $product): void
    {
        $this->products[$product->id] = $product;
    }

    public function removeProduct(int $id): void
    {
        unset($this->products[$id]);
    }

    public function searchProduct(string $name): ?Product
    {
        foreach ($this->products as $product) {
            if ($product->name === $name) {
                return $product;
            }
        }

        return null;
    }

    public function getProducts(): array
    {
        return array_values($this->products);
    }
}
