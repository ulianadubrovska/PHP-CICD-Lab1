@extends('layouts.app')

@section('title', 'Доставки — Fashion Store')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold">Доставки замовлень</h1>

    <a href="{{ route('shop.deliveries.create') }}"
       class="btn btn-primary">
        + Додати доставку
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
                        <th>Одержувач</th>
                        <th>Місто</th>
                        <th>Спосіб</th>
                        <th>Статус</th>
                        <th>Дії</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->id }}</td>
                            <td>№{{ $delivery->order_id }}</td>
                            <td>{{ $delivery->recipient_name }}</td>
                            <td>{{ $delivery->city }}</td>
                            <td>{{ $delivery->delivery_method }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $delivery->status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('shop.deliveries.edit', $delivery->id) }}"
                                       class="btn btn-sm btn-outline-warning">
                                        Редагувати
                                    </a>

                                    <form
                                        action="{{ route('shop.deliveries.destroy', $delivery->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Видалити доставку?')"
                                    >
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
                            <td colspan="7" class="text-center py-3">
                                Записів про доставки немає.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $deliveries->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
