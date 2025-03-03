{{-- resources/views/orders/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Заказ #{{ $order->id }}</h1>

    <p><strong>Дата создания:</strong> {{ $order->created_at }}</p>
    <p><strong>Статус:</strong> {{ $order->status }}</p>
    <p><strong>Телефон:</strong> {{ $order->phone }}</p>
    <p><strong>Email:</strong> {{ $order->email }}</p>
    <p><strong>Адрес:</strong> {{ $order->address }}</p>
    <p><strong>Время доставки:</strong> {{ $order->delivery_time }}</p>
    <p><strong>Сумма:</strong> {{ $order->total_price }} руб.</p>

    <h2>Товары:</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Название</th>
                <th>Цена</th>
                <th>Количество</th>
                <th>Сумма</th>
            </tr>
        </thead>
         <tbody>
          @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->price }} руб.</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->price * $item->quantity }} руб.</td>
            </tr>
          @endforeach
        </tbody>
    </table>
     <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">Назад к списку заказов</a>
</div>
@endsection