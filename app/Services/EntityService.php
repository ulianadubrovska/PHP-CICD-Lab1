<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LoggerInterface;
use App\Models\AbstractEntity;
use InvalidArgumentException;

class EntityService
{
    // Dependency Injection:
    // отримуємо логер через конструктор
    public function __construct(
        private LoggerInterface $logger
    ) {}

    // Лабораторна №3: розрахунок вартості
    public function calculateTotal(
        float $price,
        int $quantity
    ): float {
        $total = $price * $quantity;

        $this->logger->log(
            'Обчислено загальну вартість покупки'
        );

        return $total;
    }

    // Лабораторна №3: застосування знижки
    public function applyDiscount(
        float $price,
        float $discount
    ): float {
        if ($discount < 0 || $discount > 100) {
            throw new InvalidArgumentException(
                'Неприпустиме значення знижки!'
            );
        }

        $result = $price - ($price * $discount / 100);

        $this->logger->log(
            "Застосовано знижку {$discount}%"
        );

        return $result;
    }

    // Лабораторна №4: робота з моделями
    public function showEntity(
        AbstractEntity $entity
    ): void {
        $this->logger->log(
            'Отримано інформацію про об’єкт'
        );

        echo $entity->getDetails()."\n";
    }
}
