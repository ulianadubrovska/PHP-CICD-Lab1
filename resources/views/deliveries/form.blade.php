@extends('layouts.app')

@section('title', 'Керування доставкою — Fashion Store')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 fw-bold mb-4">
                    {{ $delivery ? 'Редагувати доставку' : 'Додати доставку' }}
                </h1>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Виправте помилки у формі.
                    </div>
                @endif

                <form action="{{ $delivery
                    ? route('shop.deliveries.update', $delivery->id)
                    : route('shop.deliveries.store') }}"
                    method="POST">
                    @csrf

                    @if ($delivery)
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="order_id" class="form-label">
                            Замовлення
                        </label>

                        <select
                            name="order_id"
                            id="order_id"
                            class="form-select @error('order_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Оберіть замовлення</option>

                            @foreach ($orders as $order)
                                <option
                                    value="{{ $order->id }}"
                                    @selected(old('order_id', $delivery->order_id ?? '') == $order->id)
                                >
                                    №{{ $order->id }}
                                    — {{ $order->user->name ?? 'Покупець' }}
                                </option>
                            @endforeach
                        </select>

                        @error('order_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-form.input
                        name="delivery_method"
                        label="Спосіб доставки"
                        :value="$delivery->delivery_method ?? 'nova_poshta'"
                        required
                    />

                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input
                                name="recipient_name"
                                label="Ім'я одержувача"
                                :value="$delivery->recipient_name ?? ''"
                                required
                            />
                        </div>

                        <div class="col-md-6">
                            <x-form.input
                                name="recipient_phone"
                                label="Телефон одержувача"
                                type="tel"
                                :value="$delivery->recipient_phone ?? ''"
                                required
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input
                                name="city"
                                label="Місто"
                                :value="$delivery->city ?? ''"
                                required
                            />
                        </div>

                        <div class="col-md-6">
                            <x-form.input
                                name="postal_code"
                                label="Поштовий індекс"
                                :value="$delivery->postal_code ?? ''"
                            />
                        </div>
                    </div>

                    <x-form.input
                        name="address"
                        label="Адреса доставки"
                        :value="$delivery->address ?? ''"
                        required
                    />

                    <div class="mb-4">
                        <label for="status" class="form-label">
                            Статус доставки
                        </label>

                        <select name="status" id="status"
                                class="form-select @error('status') is-invalid @enderror">
                            @foreach ([
                                'pending' => 'Очікує',
                                'shipped' => 'Відправлено',
                                'delivered' => 'Доставлено'
                            ] as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(old('status', $delivery->status ?? 'pending') === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ $delivery ? 'Зберегти зміни' : 'Створити доставку' }}
                    </button>

                    <a href="{{ route('shop.deliveries.index') }}"
                       class="btn btn-outline-secondary">
                        Скасувати
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
