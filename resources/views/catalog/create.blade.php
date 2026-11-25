@extends('layouts.app')

@section('title', 'Додати товар — Fashion Store')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 fw-bold mb-4">
                    Додати новий товар
                </h1>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Перевірте правильність заповнення форми.
                    </div>
                @endif

                <form
                    action="{{ route('shop.catalog.store') }}"
                    method="POST"
                >
                    @csrf

                    @include('catalog._form', [
                        'product' => null,
                        'buttonText' => 'Додати товар'
                    ])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
