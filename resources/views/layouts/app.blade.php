<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Магазин одягу')</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            Fashion Store
        </a>

        <div class="navbar-nav">
            <a class="nav-link" href="{{ route('home') }}">
                Головна
            </a>

            <a class="nav-link" href="{{ route('shop.catalog.index') }}">
                Каталог
            </a>

            <a class="nav-link" href="{{ route('shop.categories.index') }}">
                Категорії
            </a>

            <a class="nav-link" href="{{ route('shop.orders.index') }}">
                Замовлення
            </a>

            <a class="nav-link" href="{{ route('shop.payments.index') }}">
                Оплати
            </a>

            <a class="nav-link" href="{{ route('shop.carts.index') }}">
                Кошики
            </a>

            <a class="nav-link" href="{{ route('shop.reviews.index') }}">
                Відгуки
            </a>

            <a class="nav-link" href="{{ route('shop.users.index') }}">
                Покупці
            </a>

            <a class="nav-link" href="{{ route('shop.deliveries.index') }}">
                Доставки
            </a>

        @guest
            <a class="nav-link" href="{{ route('login') }}">
                Увійти
            </a>

            <a class="nav-link" href="{{ route('register') }}">
                Реєстрація
            </a>
        </a>
        @endguest


@auth
    <span class="navbar-text ms-3">
        {{ Auth::user()->name }}
    </span>

    <form action="{{ route('logout') }}"
          method="POST"
          class="ms-2 d-flex align-items-center">
        @csrf

        <button type="submit"
                class="btn btn-sm btn-outline-light">
            Вийти
        </button>
    </form>
@endauth


        </div>
    </div>
</nav>

<main class="container py-3">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Закрити"
            ></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @yield('content')

</main>

<footer class="border-top mt-5 py-4 text-center text-muted">
    Fashion Store — лабораторна робота №7
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
