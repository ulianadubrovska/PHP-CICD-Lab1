<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ShopCartController extends Controller
{
    public function index(): View
    {
        $carts = Cart::with('user')
            ->withCount('cartItems')
            ->orderByDesc('id')
            ->paginate(10);

        return view('carts.index', compact('carts'));
    }

    public function show(int $id): View
    {
        $cart = Cart::with(['user', 'cartItems.product'])
            ->findOrFail($id);

        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get();

        $totalCents = 0;

        foreach ($cart->cartItems as $item) {
            $priceCents = (int) round(
                (float) $item->product->price * 100
            );

            $totalCents += $priceCents * $item->quantity;
        }

        $total = $totalCents / 100;

        return view('carts.show', compact('cart', 'total', 'products'));
    }

    public function storeItem(Request $request, int $id): RedirectResponse
    {
        $cart = Cart::findOrFail($id);

        $validated = $request->validate([
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')
                    ->where('is_active', true),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $item = $cart->cartItems()
            ->where('product_id', $product->id)
            ->first();

        $newQuantity = ($item?->quantity ?? 0)
            + (int) $validated['quantity'];

        if ($newQuantity > $product->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Недостатньо товару на складі.',
            ]);
        }

        if ($item) {
            $item->update(['quantity' => $newQuantity]);
        } else {
            $cart->cartItems()->create([
                'product_id' => $product->id,
                'quantity' => $newQuantity,
            ]);
        }

        return redirect()
            ->route('shop.carts.show', $cart->id)
            ->with('success', 'Товар додано до кошика!');
    }

    public function updateItem(
        Request $request,
        int $id,
        int $itemId
    ): RedirectResponse {
        $cart = Cart::findOrFail($id);

        $item = $cart->cartItems()
            ->with('product')
            ->findOrFail($itemId);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($validated['quantity'] > $item->product->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Недостатньо товару на складі.',
            ]);
        }

        $item->update([
            'quantity' => $validated['quantity'],
        ]);

        return redirect()
            ->route('shop.carts.show', $cart->id)
            ->with('success', 'Кількість товару оновлено!');
    }

    public function destroyItem(int $id, int $itemId): RedirectResponse
    {
        $cart = Cart::findOrFail($id);

        $item = $cart->cartItems()->findOrFail($itemId);
        $item->delete();

        return redirect()
            ->route('shop.carts.show', $cart->id)
            ->with('success', 'Товар видалено з кошика!');
    }
}
