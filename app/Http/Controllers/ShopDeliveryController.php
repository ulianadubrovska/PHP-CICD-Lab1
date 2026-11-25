<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShopDeliveryController extends Controller
{
    public function index(): View
    {
        $deliveries = Delivery::with('order.user')
            ->orderByDesc('id')
            ->paginate(10);

        return view('deliveries.index', compact('deliveries'));
    }

    public function create(): View
    {
        return view('deliveries.form', [
            'delivery' => null,
            'orders' => Order::with('user')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    private function validatedData(
        Request $request,
        ?Delivery $delivery = null
    ): array {
        return $request->validate([
            'order_id' => [
                'required',
                'integer',
                'exists:orders,id',
                Rule::unique('deliveries', 'order_id')
                    ->ignore($delivery?->id),
            ],
            'delivery_method' => ['required', 'string', 'max:100'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'status' => [
                'required',
                Rule::in(['pending', 'shipped', 'delivered']),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $validated['delivered_at'] =
            $validated['status'] === 'delivered' ? now() : null;

        Delivery::create($validated);

        return redirect()
            ->route('shop.deliveries.index')
            ->with('success', 'Доставку успішно створено!');
    }

    public function edit(int $id): View
    {
        return view('deliveries.form', [
            'delivery' => Delivery::findOrFail($id),
            'orders' => Order::with('user')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $delivery = Delivery::findOrFail($id);
        $validated = $this->validatedData($request, $delivery);

        $validated['delivered_at'] =
            $validated['status'] === 'delivered'
                ? ($delivery->delivered_at ?? now())
                : null;

        $delivery->update($validated);

        return redirect()
            ->route('shop.deliveries.index')
            ->with('success', 'Доставку успішно оновлено!');
    }

    public function destroy(int $id): RedirectResponse
    {
        Delivery::findOrFail($id)->delete();

        return redirect()
            ->route('shop.deliveries.index')
            ->with('success', 'Доставку успішно видалено!');
    }
}
