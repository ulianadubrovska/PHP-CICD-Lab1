@extends('layouts.app')

@section('title', 'Керування оплатою — Fashion Store')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 fw-bold mb-4">
                    {{ $payment ? 'Редагування оплати' : 'Додати оплату' }}
                </h1>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Перевірте правильність введених даних.
                    </div>
                @endif

                <form action="{{ $payment
                    ? route('shop.payments.update', $payment->id)
                    : route('shop.payments.store') }}"
                    method="POST">
                    @csrf

                    @if ($payment)
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="order_id" class="form-label">
                            Замовлення
                        </label>

                        <select name="order_id"
                                id="order_id"
                                class="form-select @error('order_id') is-invalid @enderror"
                                required>
                            <option value="">Оберіть замовлення</option>

                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}"
                                    @selected(old('order_id', $payment->order_id ?? '') == $order->id)>
                                    №{{ $order->id }}
                                    — {{ $order->user->name ?? 'Покупець' }}
                                    — {{ $order->total_amount }} грн
                                </option>
                            @endforeach
                        </select>

                        @error('order_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-form.input
                        name="payment_method"
                        label="Спосіб оплати"
                        :value="$payment->payment_method ?? 'card'"
                        required
                    />

                    <x-form.input
                        name="amount"
                        label="Сума оплати (грн)"
                        type="number"
                        min="0"
                        step="0.01"
                        :value="$payment->amount ?? ''"
                        required
                    />

                    <div class="mb-4">
                        <label for="status" class="form-label">
                            Статус
                        </label>

                        <select name="status"
                                id="status"
                                class="form-select @error('status') is-invalid @enderror"
                                required>
                            @foreach (['pending' => 'Очікує', 'paid' => 'Оплачено', 'failed' => 'Помилка'] as $value => $label)
                                <option value="{{ $value }}"
                                    @selected(old('status', $payment->status ?? 'pending') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ $payment ? 'Зберегти зміни' : 'Створити оплату' }}
                    </button>

                    <a href="{{ route('shop.payments.index') }}"
                       class="btn btn-outline-secondary">
                        Скасувати
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
