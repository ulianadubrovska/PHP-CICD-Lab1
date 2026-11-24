<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function summary(): JsonResponse
    {
        $items = [
            [
                'product' => 'Футболка Oversize',
                'quantity' => 2,
                'unit_price' => 1200.0,
            ],
            [
                'product' => 'Шкіряний ремінь',
                'quantity' => 1,
                'unit_price' => 700.0,
            ],
        ];

        $total = 0.0;

        foreach ($items as $item) {
            $total += $item['quantity'] * $item['unit_price'];
        }

        return response()->json([
            'status' => 'success',
            'items' => $items,
            'total' => $total,
            'currency' => 'UAH',
        ], 200);
    }
}
