<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $orders = Order::query()
            ->with(['user', 'products', 'payment', 'delivery'])
            ->where('user_id', $validated['user_id'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with([
            'user',
            'products',
            'payment',
            'delivery',
        ])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $order,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('products', 'id')
                    ->where('is_active', true),
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $order = DB::transaction(function () use ($validated) {
            $order = Order::create([
                'user_id' => $validated['user_id'],
                'ordered_at' => now(),
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            $totalCents = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];

                $priceCents = (int) round(
                    (float) $product->price * 100
                );

                $totalCents += $priceCents * $quantity;

                $order->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                ]);
            }

            $order->update([
                'total_amount' => $totalCents / 100,
            ]);

            return $order;
        });

        $order->load([
            'user',
            'products',
            'payment',
            'delivery',
        ]);

        return response()->json([
            'status' => 'created',
            'data' => $order,
        ], 201);
    }
}
