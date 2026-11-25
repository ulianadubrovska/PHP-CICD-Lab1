@extends('layouts.app')

@section('title', 'Оплати — Fashion Store')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold">Оплати замовлень</h1>

    <a href="{{ route('shop.payments.create') }}"
       class="btn btn-primary">
        + Додати оплату
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Замовлення</th>
                        <th>Покупець</th>
                        <th>Спосіб оплати</th>
                        <th>Сума</th>
                        <th>Статус</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>№{{ $payment->order_id }}</td>
                            <td>{{ $payment->order->user->name ?? '—' }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>
                                {{ number_format((float) $payment->amount, 2, ',', ' ') }}
                                грн
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $payment->status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('shop.payments.edit', $payment->id) }}"
                                       class="btn btn-sm btn-outline-warning">
                                        Редагувати
                                    </a>

                                    <form action="{{ route('shop.payments.destroy', $payment->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Видалити запис про оплату?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger">
                                            Видалити
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                Записів про оплату немає.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $payments->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
