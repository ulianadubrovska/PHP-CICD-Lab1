@extends('layouts.app')

@section('title', 'Кошики покупців — Fashion Store')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold">Кошики покупців</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Покупець</th>
                        <th>Кількість позицій</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($carts as $cart)
                        <tr>
                            <td>{{ $cart->id }}</td>
                            <td>{{ $cart->user->name ?? '—' }}</td>
                            <td>{{ $cart->cart_items_count }}</td>
                            <td>
                                <a href="{{ route('shop.carts.show', $cart->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Переглянути кошик
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                Кошиків поки немає.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $carts->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
