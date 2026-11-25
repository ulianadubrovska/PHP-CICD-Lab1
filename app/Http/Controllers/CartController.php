<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $cart = Cart::query()
            ->with('cartItems.product')
            ->where('user_id', $validated['user_id'])
            ->firstOrFail();

        $items = [];
        $totalCents = 0;

        foreach ($cart->cartItems as $item) {
            $product = $item->product;
            $priceCents = (int) round((float) $product->price * 100);
            $subtotalCents = $priceCents * $item->quantity;

            $totalCents += $subtotalCents;

            $items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $item->quantity,
                'subtotal' => number_format(
                    $subtotalCents / 100,
                    2,
                    '.',
                    ''
                ),
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'cart_id' => $cart->id,
                'user_id' => $cart->user_id,
                'items' => $items,
                'total' => number_format(
                    $totalCents / 100,
                    2,
                    '.',
                    ''
                ),
            ],
        ]);
    }
}
