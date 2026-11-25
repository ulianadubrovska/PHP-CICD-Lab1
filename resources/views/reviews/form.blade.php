@extends('layouts.app')

@section('title', 'Керування відгуком — Fashion Store')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 fw-bold mb-4">
                    {{ $review ? 'Редагування відгуку' : 'Додати відгук' }}
                </h1>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Перевірте введені дані.
                    </div>
                @endif

                <form action="{{ $review
                    ? route('shop.reviews.update', $review->id)
                    : route('shop.reviews.store') }}"
                    method="POST">
                    @csrf

                    @if ($review)
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label" for="user_id">Покупець</label>
                        <select name="user_id" id="user_id"
                                class="form-select @error('user_id') is-invalid @enderror"
                                required>
                            <option value="">Оберіть покупця</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    @selected(old('user_id', $review->user_id ?? '') == $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="product_id">Товар</label>
                        <select name="product_id" id="product_id"
                                class="form-select @error('product_id') is-invalid @enderror"
                                required>
                            <option value="">Оберіть товар</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    @selected(old('product_id', $review->product_id ?? '') == $product->id)>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-form.input
                        name="rating"
                        label="Оцінка від 1 до 5"
                        type="number"
                        min="1"
                        max="5"
                        :value="$review->rating ?? 5"
                        required
                    />

                    <div class="mb-3">
                        <label class="form-label" for="comment">
                            Коментар
                        </label>
                        <textarea name="comment"
                                  id="comment"
                                  rows="4"
                                  class="form-control @error('comment') is-invalid @enderror"
                        >{{ old('comment', $review->comment ?? '') }}</textarea>

                        @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ $review ? 'Зберегти зміни' : 'Додати відгук' }}
                    </button>

                    <a href="{{ route('shop.reviews.index') }}"
                       class="btn btn-outline-secondary">
                        Скасувати
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
