@extends('layouts.app')

@section('title', 'Кошик покупця — Fashion Store')

@section('content')
<a href="{{ route('shop.carts.index') }}"
   class="btn btn-outline-secondary mb-4">
    ← Назад до кошиків
</a>

<h1 class="fw-bold mb-3">
    Кошик №{{ $cart->id }}
</h1>

<p class="text-secondary">
    Покупець: {{ $cart->user->name ?? '—' }}
</p>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h4 class="mb-3">Додати товар до кошика</h4>

        <form action="{{ route('shop.carts.items.store', $cart->id) }}"
              method="POST">
            @csrf

            <div class="row g-3 align-items-end">
                <div class="col-md-7">
                    <label for="product_id" class="form-label">
                        Товар
                    </label>

                    <select name="product_id"
                            id="product_id"
                            class="form-select @error('product_id') is-invalid @enderror"
                            required>
                        <option value="">Оберіть товар</option>

                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                @selected(old('product_id') == $product->id)>
                                {{ $product->name }}
                                — {{ $product->price }} грн
                            </option>
                        @endforeach
                    </select>

                    @error('product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label for="quantity" class="form-label">
                        Кількість
                    </label>

                    <input type="number"
                           name="quantity"
                           id="quantity"
                           min="1"
                           value="{{ old('quantity', 1) }}"
                           class="form-control @error('quantity') is-invalid @enderror"
                           required>

                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        + Додати
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Товар</th>
                        <th>Ціна</th>
                        <th>Кількість</th>
                        <th>Сума</th>
                        <th>Дії</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($cart->cartItems as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>

                            <td>
                                {{ number_format((float) $item->product->price, 2, ',', ' ') }}
                                грн
                            </td>

                            <td>
                                <form
                                    action="{{ route('shop.carts.items.update', [$cart->id, $item->id]) }}"
                                    method="POST"
                                    class="d-flex gap-2"
                                >
                                    @csrf
                                    @method('PUT')

                                    <input
                                        type="number"
                                        name="quantity"
                                        min="1"
                                        value="{{ $item->quantity }}"
                                        class="form-control"
                                        style="width: 90px"
                                        required
                                    >

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-primary">
                                        Оновити
                                    </button>
                                </form>
                            </td>

                            <td>
                                {{ number_format(
                                    (float) $item->product->price * $item->quantity,
                                    2,
                                    ',',
                                    ' '
                                ) }} грн
                            </td>

                            <td>
                                <form
                                    action="{{ route('shop.carts.items.destroy', [$cart->id, $item->id]) }}"
                                    method="POST"
                                    onsubmit="return confirm('Видалити товар із кошика?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger">
                                        Видалити
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3">
                                Кошик порожній.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h4 class="text-end mt-4">
            Загальна сума:
            {{ number_format($total, 2, ',', ' ') }} грн
        </h4>
    </div>
</div>
@endsection
