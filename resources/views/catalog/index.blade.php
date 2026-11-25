@extends('layouts.app')

@section('title', 'Каталог товарів — Fashion Store')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold">Каталог товарів</h1>
        <p class="text-secondary mb-0">
            Асортимент одягу та аксесуарів нашого магазину
        </p>
        <a href="{{ route('shop.catalog.create') }}"
            class="btn btn-primary">
            + Додати товар
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Назва</th>
                        <th>Категорія</th>
                        <th>Артикул</th>
                        <th>Ціна</th>
                        <th>Кількість</th>
                        <th>Відгуки</th>
                        <th>Дії</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>

                            <td class="fw-semibold">
                                {{ $product->name }}
                            </td>

                            <td>
                                {{ $product->category->name ?? 'Без категорії' }}
                            </td>

                            <td>{{ $product->sku }}</td>

                            <td>
                                {{ number_format((float) $product->price, 2, ',', ' ') }}
                                грн
                            </td>

                            <td>{{ $product->stock_quantity }}</td>

                            <td>{{ $product->reviews_count }}</td>


<td>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('shop.catalog.show', $product->id) }}"
           class="btn btn-sm btn-outline-primary">
            Перегляд
        </a>

        <a href="{{ route('shop.catalog.edit', $product->id) }}"
           class="btn btn-sm btn-outline-warning">
            Редагувати
        </a>

        <form action="{{ route('shop.catalog.destroy', $product->id) }}"
              method="POST"
              onsubmit="return confirm('Видалити цей товар?')">
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
                            <td colspan="8" class="text-center py-4">
                                Товарів поки немає.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
