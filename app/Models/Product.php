<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\ExportableInterface;
use App\Traits\JsonExportTrait;

class Product extends AbstractEntity implements ExportableInterface
{
    use JsonExportTrait;

    public function __construct(
        string $id,
        string $createdAt,
        private string $name,
        private float $price,
        private int $stock
    ) {
        parent::__construct($id, $createdAt);
    }

    public function getDetails(): string
    {
        return "Товар: {$this->name}, ціна: {$this->price} грн, залишок: {$this->stock}";
    }
}
