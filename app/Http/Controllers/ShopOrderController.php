<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShopOrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['user', 'products'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        return view('orders.create', [
            'order' => null,
            'users' => User::orderBy('name')->get(),
            'products' => Product::where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function edit(int $id): View
    {
        $order = Order::with(['products', 'payment', 'delivery'])
            ->findOrFail($id);

        return view('orders.create', [
            'order' => $order,
            'users' => User::orderBy('name')->get(),
            'products' => Product::where('is_active', true)
                ->orWhereHas('orders', function ($query) use ($order) {
                    $query->where('orders.id', $order->id);
                })
                ->orderBy('name')
                ->get(),
        ]);
    }

    private function orderData(Request $request): array
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => [
                'required',
                'integer',
                'distinct',
                'exists:products,id',
            ],
            'quantities' => ['required', 'array'],
        ]);

        $pivotData = [];
        $totalCents = 0;

        foreach ($validated['products'] as $productId) {
            $product = Product::findOrFail($productId);

            if (! $product->is_active) {
                // Не дозволяємо додавати неактивні товари.
                throw ValidationException::withMessages([
                    'products' => 'Один із вибраних товарів неактивний.',
                ]);
            }

            $quantity = $request->input(
                "quantities.$productId"
            );

            if (
                filter_var(
                    $quantity,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                ) === false
            ) {
                throw ValidationException::withMessages([
                    'quantities' => 'Вкажіть коректну кількість для кожного вибраного товару.',
                ]);
            }

            $quantity = (int) $quantity;

            if ($quantity > $product->stock_quantity) {
                throw ValidationException::withMessages([
                    'quantities' => "Недостатній залишок товару: {$product->name}.",
                ]);
            }

            $priceCents = (int) round(
                (float) $product->price * 100
            );

            $totalCents += $priceCents * $quantity;

            $pivotData[$product->id] = [
                'quantity' => $quantity,
                'unit_price' => $product->price,
            ];
        }

        return [
            'user_id' => $validated['user_id'],
            'pivot_data' => $pivotData,
            'total_amount' => $totalCents / 100,
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->orderData($request);

        DB::transaction(function () use ($data) {
            $order = Order::create([
                'user_id' => $data['user_id'],
                'ordered_at' => now(),
                'status' => 'pending',
                'total_amount' => $data['total_amount'],
            ]);

            $order->products()->sync($data['pivot_data']);
        });

        return redirect()
            ->route('shop.orders.index')
            ->with('success', 'Замовлення успішно створено!');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'pending'
            || $order->payment()->where('status', 'paid')->exists()
            || $order->delivery()->whereIn('status', ['shipped', 'delivered'])->exists()
        ) {
            return redirect()
                ->route('shop.orders.index')
                ->with('error', 'Це замовлення вже не можна редагувати.');
        }

        $data = $this->orderData($request);

        DB::transaction(function () use ($order, $data) {
            $order->update([
                'user_id' => $data['user_id'],
                'total_amount' => $data['total_amount'],
            ]);

            $order->products()->sync($data['pivot_data']);
        });

        return redirect()
            ->route('shop.orders.index')
            ->with('success', 'Замовлення успішно оновлено!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        if (
            $order->payment()->exists()
            || $order->delivery()->exists()
            || $order->status !== 'pending'
        ) {
            return redirect()
                ->route('shop.orders.index')
                ->with('error', 'Не можна видалити замовлення з оплатою, доставкою або завершеним статусом.');
        }

        $order->delete();

        return redirect()
            ->route('shop.orders.index')
            ->with('success', 'Замовлення успішно видалено!');
    }
}
