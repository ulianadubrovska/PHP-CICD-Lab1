@extends('layouts.app')

@section('title', 'Керування замовленням — Fashion Store')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h1 class="fw-bold mb-4">
            {{ $order ? 'Редагування замовлення №'.$order->id : 'Створити замовлення' }}
        </h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ $order
                ? route('shop.orders.update', $order->id)
                : route('shop.orders.store') }}"
            method="POST"
        >
            @csrf

            @if ($order)
                @method('PUT')
            @endif

            <div class="mb-4">
                <label for="user_id" class="form-label">
                    Покупець
                </label>

                <select name="user_id"
                        id="user_id"
                        class="form-select"
                        required>
                    <option value="">Оберіть покупця</option>

                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                            @selected(old('user_id', $order->user_id ?? '') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <h4>Товари замовлення</h4>
            <p class="text-secondary">
                Виберіть товари та вкажіть кількість.
            </p>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Обрати</th>
                            <th>Товар</th>
                            <th>Ціна</th>
                            <th>Кількість</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($products as $product)
                            @php
                                $existing = $order?->products
                                    ->firstWhere('id', $product->id);

                                $selected = old(
                                    'products',
                                    $order?->products->pluck('id')->all() ?? []
                                );

                                $quantity = old(
                                    'quantities.'.$product->id,
                                    $existing?->pivot->quantity ?? 1
                                );
                            @endphp

                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        name="products[]"
                                        value="{{ $product->id }}"
                                        class="form-check-input"
                                        @checked(in_array($product->id, $selected))
                                    >
                                </td>

                                <td>{{ $product->name }}</td>

                                <td>{{ $product->price }} грн</td>

                                <td>
                                    <input
                                        type="number"
                                        name="quantities[{{ $product->id }}]"
                                        min="1"
                                        value="{{ $quantity }}"
                                        class="form-control"
                                        style="max-width: 110px"
                                    >
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $order ? 'Зберегти зміни' : 'Створити замовлення' }}
            </button>

            <a href="{{ route('shop.orders.index') }}"
               class="btn btn-outline-secondary">
                Скасувати
            </a>
        </form>
    </div>
</div>
@endsection
