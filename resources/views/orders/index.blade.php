@extends('layouts.app')

@section('title', 'Замовлення — Fashion Store')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold">Замовлення</h1>
        <p class="text-secondary mb-0">
            Перегляд та керування замовленнями покупців
        </p>
    </div>

    <a href="{{ route('shop.orders.create') }}"
       class="btn btn-primary">
        + Нове замовлення
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Покупець</th>
                        <th>Товари</th>
                        <th>Сума</th>
                        <th>Статус</th>
                        <th>Дії</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>

                            <td>
                                {{ $order->user->name ?? '—' }}
                            </td>

                            <td>
                                @forelse ($order->products as $product)
                                    <div class="mb-1">
                                        {{ $product->name }}
                                        × {{ $product->pivot->quantity }}
                                    </div>
                                @empty
                                    <span class="text-secondary">
                                        Немає товарів
                                    </span>
                                @endforelse
                            </td>

                            <td>
                                {{ number_format(
                                    (float) $order->total_amount,
                                    2,
                                    ',',
                                    ' '
                                ) }} грн
                            </td>

                            <td>
                                @if ($order->status === 'pending')
                                    <span class="badge bg-warning text-dark">
                                        Очікує
                                    </span>
                                @elseif ($order->status === 'completed')
                                    <span class="badge bg-success">
                                        Завершено
                                    </span>
                                @elseif ($order->status === 'cancelled')
                                    <span class="badge bg-danger">
                                        Скасовано
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        {{ $order->status }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex gap-2">

                                    <a href="{{ route('shop.orders.edit', $order->id) }}"
                                       class="btn btn-sm btn-outline-warning">
                                        Редагувати
                                    </a>

                                    <form
                                        action="{{ route('shop.orders.destroy', $order->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Ви впевнені, що хочете видалити це замовлення?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                        >
                                            Видалити
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                Замовлень поки немає.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="mt-3">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>

    </div>
</div>

@endsection
