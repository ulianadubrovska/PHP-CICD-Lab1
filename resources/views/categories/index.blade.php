@extends('layouts.app')

@section('title', 'Категорії — Fashion Store')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold">Категорії товарів</h1>

    <a href="{{ route('shop.categories.create') }}"
       class="btn btn-primary">
        + Додати категорію
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Назва</th>
                        <th>Slug</th>
                        <th>Опис</th>
                        <th>Товарів</th>
                        <th>Дії</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->description ?: '—' }}</td>
                            <td>{{ $category->products_count }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('shop.categories.edit', $category->id) }}"
                                       class="btn btn-sm btn-outline-warning">
                                        Редагувати
                                    </a>

                                    <form action="{{ route('shop.categories.destroy', $category->id) }}"
                                          method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Видалити категорію?')">
                                            Видалити
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                Категорій поки немає.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $categories->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
