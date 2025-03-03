{{-- resources/views/cart/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ваша корзина</h1>

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

    @if ($cart && $cart->items->isNotEmpty())
        <table class="table">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart->items as $item)
                    <tr>
                        <td>
                            <a href="{{ route('products.show', $item->product) }}">{{ $item->product->name }}</a>
                        </td>
                        <td>{{ $item->product->price }} руб.</td>
                        <td>
                            <form action="{{ route('cart.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" style="width: 50px;">
                                <button type="submit" class="btn btn-sm btn-primary">Обновить</button>
                            </form>
                        </td>
                        <td>{{ $item->product->price * $item->quantity }} руб.</td>
                        <td>
                            <form action="{{ route('cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>Итого:</strong></td>
                    <td><strong>{{ $cart->items->sum(function ($item) { return $item->product->price * $item->quantity; }) }} руб.</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        @auth
   <form action="{{ route('orders.store') }}" method="POST">
      @csrf
       <div class="mb-3">
          <label for="phone" class="form-label">Телефон:</label>
          <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
      </div>
      <div class="mb-3">
         <label for="email" class="form-label">Email:</label>
          <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
       </div>
      <div class="mb-3">
         <label for="address" class="form-label">Адрес:</label>
           <textarea class="form-control" id="address" name="address" required>{{ old('address') }}</textarea>
      </div>

       <div class="mb-3">
         <label for="delivery_time" class="form-label">Желаемое время доставки:</label>
          <input type="datetime-local" class="form-control" id="delivery_time" name="delivery_time" value="{{ old('delivery_time') }}">
      </div>

      <button type="submit" class="btn btn-success">Оформить заказ</button>
    </form>
  @else
   <p>Чтобы оформить заказ, пожалуйста, <a href="{{ route('login') }}">войдите</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a>.</p>
 @endauth



    @else
        <p>Ваша корзина пуста.</p>
    @endif
      <a href="/products" class="btn btn-secondary mt-3">Вернуться к покупкам</a>
</div>
@endsection