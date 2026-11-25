@extends('layouts.app')

@section('title', 'Головна — Fashion Store')

@section('content')
<div class="p-5 mb-4 bg-white rounded-4 shadow-sm">
    <span class="badge bg-primary mb-3">
        Інтернет-магазин одягу та аксесуарів
    </span>

    <h1 class="display-5 fw-bold">
        Ласкаво просимо до Fashion Store!
    </h1>

    <p class="fs-5 text-secondary mt-3">
        Каталог одягу та аксесуарів з можливістю
        перегляду, додавання, редагування
        та керування товарами.
    </p>

    <a href="{{ route('shop.catalog.index') }}"
       class="btn btn-primary btn-lg mt-3">
        Перейти до каталогу
    </a>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h4>Каталог товарів</h4>
                <p>Перегляд асортименту магазину.</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h4>Категорії</h4>
                <p>Організація товарів за категоріями.</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h4>Замовлення</h4>
                <p>Керування замовленнями покупців.</p>
            </div>
        </div>
    </div>
</div>
@endsection
