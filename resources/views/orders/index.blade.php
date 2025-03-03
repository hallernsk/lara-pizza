{{-- resources/views/orders/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Мои заказы</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($orders->isEmpty())
            <p>У вас пока нет заказов.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>ID заказа</th>
                        <th>Дата создания</th>
                        <th>Статус</th>
                        <th>Сумма</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->created_at }}</td>
                            <td>{{ $order->status }}</td>
                            <td>{{ $order->total_price }} руб.</td>
                            <td>
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm">Подробнее</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection