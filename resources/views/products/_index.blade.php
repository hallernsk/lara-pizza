@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Наши пиццы и напитки</h1>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        @forelse ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                    @else
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Placeholder">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">{{ $product->description }}</p>
                        <p class="card-text">Цена: {{ $product->price }} руб.</p>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-primary">Подробнее</a>

                        {{-- Кнопка "В корзину" - только для авторизованных НЕ-админов --}}
                        @auth
                            @if (!Auth::user()->is_admin)
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="btn btn-success">Добавить в корзину</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>Товаров пока нет.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection