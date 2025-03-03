@extends('layouts.app')

@section('content')
<div class="container">
    @auth  {{-- Проверяем, авторизован ли пользователь --}}
        @if (Auth::user()->is_admin)
            {{-- Если АДМИНИСТРАТОР, показываем админский контент --}}
            <h1>Список товаров (Админ-панель)</h1>
            <a href="{{ route('admin.products.create') }}" class="btn btn-success mb-3">Добавить товар</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">Главная страница админ панели</a>

             <table class="table">
                <thead>
                {{-- ... (заголовок таблицы) ... --}}
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                         {{-- ... (данные товара) ... --}}
                            <td>
                                 <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info btn-sm">Смотреть</a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm">Редактировать</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены?')">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Товаров пока нет.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @else
            {{-- Если ОБЫЧНЫЙ ПОЛЬЗОВАТЕЛЬ, показываем обычный контент --}}
            <h1>Наши пиццы и напитки</h1>

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
                                 @if (!Auth::user()->is_admin)
                                   <form action="{{ route('cart.add') }}" method="POST">
                                      @csrf
                                      <input type="hidden" name="product_id" value="{{ $product->id }}">
                                       <button type="submit" class="btn btn-success">Добавить в корзину</button>
                                   </form>
                                 @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p>Товаров пока нет.</p>
                    </div>
                @endforelse
            </div>
        @endif
    @else
     {{-- Если НЕ АВТОРИЗОВАН, показываем обычный контент --}}
            <h1>Наши пиццы и напитки</h1>

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
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p>Товаров пока нет.</p>
                    </div>
                @endforelse
            </div>
    @endauth

</div>