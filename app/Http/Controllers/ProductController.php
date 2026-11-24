<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Демонстраційні товари інтернет-магазину.
    // У лабораторній №5 базу даних ще не використовуємо.
    private array $items = [
        [
            'id' => 1,
            'name' => 'Футболка Oversize',
            'description' => 'Бавовняна футболка вільного крою',
            'category_id' => 1,
            'price' => 1200.0,
            'stock' => 12,
        ],
        [
            'id' => 2,
            'name' => 'Джинси Straight Fit',
            'description' => 'Джинси прямого крою',
            'category_id' => 2,
            'price' => 1850.0,
            'stock' => 8,
        ],
        [
            'id' => 3,
            'name' => 'Шкіряний ремінь',
            'description' => 'Аксесуар із натуральної шкіри',
            'category_id' => 5,
            'price' => 700.0,
            'stock' => 20,
        ],
    ];

    // GET /catalog — отримання всіх товарів.
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'count' => count($this->items),
            'data' => $this->items,
        ], 200);
    }

    // GET /catalog/{id} — пошук конкретного товару.
    public function show(string $id): JsonResponse
    {
        foreach ($this->items as $item) {
            if ($item['id'] === (int) $id) {
                return response()->json([
                    'status' => 'success',
                    'data' => $item,
                ], 200);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Товар із таким ID не знайдено.',
        ], 404);
    }

    // POST /catalog — створення нового товару.
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['required', 'string', 'min:5'],
            'category_id' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $newItem = [
            'id' => count($this->items) + 1,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category_id' => (int) $validated['category_id'],
            'price' => (float) $validated['price'],
            'stock' => (int) $validated['stock'],
        ];

        return response()->json([
            'status' => 'created',
            'data' => $newItem,
        ], 201);
    }
}
