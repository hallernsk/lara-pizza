{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Наши пиццы и напитки</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
     @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                        {{-- Админские кнопки --}}
                        @auth
                           @if (Auth::user()->is_admin)
                             <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm">Редактировать</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: inline-block;">
                                  @csrf
                                   @method('DELETE')
                                 <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены?')">Удалить</button>
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

    {{-- Секция для администратора --}}
    @auth
        @if (Auth::user()->is_admin)
            <div class="container">
              <h1>Список товаров (Админ-панель)</h1>
               <a href="{{ route('products.create') }}" class="btn btn-success mb-3">Добавить товар</a>
               <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">Главная страница админ панели</a>
           </div>
        @endif
    @endauth
</div>
@endsection