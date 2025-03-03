{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $product->name }}</h1>

        <div class="row">
            <div class="col-md-6">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid" alt="{{ $product->name }}">
                @else
                    <img src="https://via.placeholder.com/400x300" class="img-fluid" alt="Placeholder">
                @endif
            </div>
            <div class="col-md-6">
                <p><strong>Описание:</strong> {{ $product->description }}</p>
                <p><strong>Цена:</strong> {{ $product->price }} руб.</p>
                <p><strong>Тип:</strong> {{ $product->type }}</p>

                {{-- Добавляем в корзину ТОЛЬКО если пользователь авторизован и НЕ админ --}}
                @auth
                   @if (!Auth::user()->is_admin)
                     <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="btn btn-success">Отправить в корзину</button>
                    </form>
                   @endif
                @endauth

                <a href="/products" class="btn btn-secondary mt-3">Назад к списку товаров</a>
            </div>
        </div>
    </div>
@endsection
