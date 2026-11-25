@extends('layouts.app')

@section('title', 'Редагування товару — Fashion Store')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 fw-bold mb-4">
                    Редагування товару №{{ $product->id }}
                </h1>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Перевірте правильність заповнення форми.
                    </div>
                @endif

                <form
                    action="{{ route('shop.catalog.update', $product->id) }}"
                    method="POST"
                >
                    @csrf
                    @method('PUT')

                    @include('catalog._form', [
                        'buttonText' => 'Зберегти зміни'
                    ])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
