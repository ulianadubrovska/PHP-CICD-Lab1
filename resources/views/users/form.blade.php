@extends('layouts.app')

@section('title', 'Керування покупцем — Fashion Store')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 fw-bold mb-4">
                    {{ $user ? 'Редагування покупця' : 'Додати покупця' }}
                </h1>

                <form action="{{ $user
                    ? route('shop.users.update', $user->id)
                    : route('shop.users.store') }}"
                    method="POST">
                    @csrf

                    @if ($user)
                        @method('PUT')
                    @endif

                    <x-form.input
                        name="name"
                        label="Ім'я покупця"
                        :value="$user->name ?? ''"
                        required
                    />

                    <x-form.input
                        name="email"
                        label="Електронна пошта"
                        type="email"
                        :value="$user->email ?? ''"
                        required
                    />

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            {{ $user ? 'Новий пароль (необов’язково)' : 'Пароль' }}
                        </label>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            minlength="8"
                            class="form-control @error('password') is-invalid @enderror"
                            @if (!$user) required @endif
                        >

                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ $user ? 'Зберегти зміни' : 'Створити покупця' }}
                    </button>

                    <a href="{{ route('shop.users.index') }}"
                       class="btn btn-outline-secondary">
                        Скасувати
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
