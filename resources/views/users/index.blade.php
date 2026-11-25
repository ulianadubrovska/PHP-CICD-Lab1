@extends('layouts.app')

@section('title', 'Покупці — Fashion Store')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold">Покупці</h1>

    <a href="{{ route('shop.users.create') }}"
       class="btn btn-primary">
        + Додати покупця
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Ім'я</th>
                        <th>Email</th>
                        <th>Замовлень</th>
                        <th>Відгуків</th>
                        <th>Дії</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->orders_count }}</td>
                            <td>{{ $user->reviews_count }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('shop.users.edit', $user->id) }}"
                                       class="btn btn-sm btn-outline-warning">
                                        Редагувати
                                    </a>

                                    <form action="{{ route('shop.users.destroy', $user->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Видалити покупця?')">
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
                            <td colspan="6" class="text-center">
                                Покупців поки немає.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
