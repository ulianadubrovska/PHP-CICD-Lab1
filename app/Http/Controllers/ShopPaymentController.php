<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShopPaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::with('order.user')
            ->orderByDesc('id')
            ->paginate(10);

        return view('payments.index', compact('payments'));
    }

    public function create(): View
    {
        return view('payments.form', [
            'payment' => null,
            'orders' => Order::with('user')->orderByDesc('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => [
                'required',
                'integer',
                'exists:orders,id',
                'unique:payments,order_id',
            ],
            'payment_method' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['pending', 'paid', 'failed'])],
        ]);

        $validated['paid_at'] = $validated['status'] === 'paid'
            ? now()
            : null;

        Payment::create($validated);

        return redirect()
            ->route('shop.payments.index')
            ->with('success', 'Запис про оплату створено!');
    }

    public function edit(int $id): View
    {
        return view('payments.form', [
            'payment' => Payment::findOrFail($id),
            'orders' => Order::with('user')->orderByDesc('id')->get(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'order_id' => [
                'required',
                'integer',
                'exists:orders,id',
                Rule::unique('payments', 'order_id')->ignore($payment->id),
            ],
            'payment_method' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['pending', 'paid', 'failed'])],
        ]);

        $validated['paid_at'] = $validated['status'] === 'paid'
            ? ($payment->paid_at ?? now())
            : null;

        $payment->update($validated);

        return redirect()
            ->route('shop.payments.index')
            ->with('success', 'Запис про оплату оновлено!');
    }

    public function destroy(int $id): RedirectResponse
    {
        Payment::findOrFail($id)->delete();

        return redirect()
            ->route('shop.payments.index')
            ->with('success', 'Запис про оплату видалено!');
    }
}
