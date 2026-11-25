@extends('layouts.app')

@section('title', 'Відгуки — Fashion Store')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold">Відгуки покупців</h1>

    <a href="{{ route('shop.reviews.create') }}"
       class="btn btn-primary">
        + Додати відгук
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
                        <th>Товар</th>
                        <th>Оцінка</th>
                        <th>Коментар</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>{{ $review->id }}</td>
                            <td>{{ $review->user->name ?? '—' }}</td>
                            <td>{{ $review->product->name ?? '—' }}</td>
                            <td>{{ $review->rating }}/5</td>
                            <td>{{ $review->comment ?: '—' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('shop.reviews.edit', $review->id) }}"
                                       class="btn btn-sm btn-outline-warning">
                                        Редагувати
                                    </a>

                                    <form action="{{ route('shop.reviews.destroy', $review->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Видалити відгук?')">
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
                                Відгуків поки немає.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $reviews->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
