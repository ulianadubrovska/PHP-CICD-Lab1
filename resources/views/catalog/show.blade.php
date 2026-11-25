@extends('layouts.app')

@section('title', $product->name . ' — Fashion Store')

@section('content')
<a href="{{ route('shop.catalog.index') }}"
   class="btn btn-outline-secondary mb-4">
    ← Назад до каталогу
</a>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h1 class="fw-bold">{{ $product->name }}</h1>

        <span class="badge bg-primary mb-3">
            {{ $product->category->name ?? 'Без категорії' }}
        </span>

        <p class="text-secondary">
            {{ $product->description ?: 'Опис відсутній.' }}
        </p>

        <hr>

        <p><strong>Артикул:</strong> {{ $product->sku }}</p>
        <p><strong>На складі:</strong> {{ $product->stock_quantity }}</p>
        <p>
            <strong>Ціна:</strong>
            {{ number_format((float) $product->price, 2, ',', ' ') }}
            грн
        </p>

        <h4 class="mt-4">Відгуки покупців</h4>

        @forelse ($product->reviews as $review)
            <div class="border-bottom py-3">
                <strong>{{ $review->user->name ?? 'Покупець' }}</strong>
                <span class="badge bg-success">
                    {{ $review->rating }}/5
                </span>

                <p class="mb-0 mt-2">
                    {{ $review->comment }}
                </p>
            </div>
        @empty
            <p class="text-muted">Відгуків поки немає.</p>
        @endforelse
    </div>
</div>
@endsection
