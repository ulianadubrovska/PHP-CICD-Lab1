@extends('layouts.app')

@section('title', 'Реєстрація — Fashion Store')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 fw-bold mb-4">Реєстрація</h1>

                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Ім'я</label>
                        <input type="text" name="name" id="name"
                               value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Електронна пошта
                        </label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required>

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               minlength="8" required>

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            Підтвердження пароля
                        </label>
                        <input type="password" name="password_confirmation"
                               id="password_confirmation"
                               class="form-control"
                               minlength="8" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Зареєструватися
                    </button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Уже маєте обліковий запис?
                    <a href="{{ route('login') }}">Увійти</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
