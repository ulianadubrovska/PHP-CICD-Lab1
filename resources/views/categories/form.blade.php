@extends('layouts.app')

@section('title', 'Керування категорією — Fashion Store')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 fw-bold mb-4">
                    {{ $category ? 'Редагувати категорію' : 'Додати категорію' }}
                </h1>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Виправте помилки у формі.
                    </div>
                @endif

                <form
                    action="{{ $category
                        ? route('shop.categories.update', $category->id)
                        : route('shop.categories.store') }}"
                    method="POST"
                >
                    @csrf

                    @if ($category)
                        @method('PUT')
                    @endif

                    <x-form.input
                        name="name"
                        label="Назва категорії"
                        :value="$category->name ?? ''"
                        required
                    />

                    <x-form.input
                        name="slug"
                        label="Slug (наприклад, womens-clothing)"
                        :value="$category->slug ?? ''"
                        required
                    />

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Опис
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            class="form-control @error('description') is-invalid @enderror"
                            rows="4"
                        >{{ old('description', $category->description ?? '') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ $category ? 'Зберегти зміни' : 'Створити категорію' }}
                    </button>

                    <a href="{{ route('shop.categories.index') }}"
                       class="btn btn-outline-secondary">
                        Скасувати
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
