{{-- resources/views/admin/products/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Список товаров (Админ-панель)</h1>

    <a href="{{ route('admin.products.create') }}" class="btn btn-success mb-3">Добавить товар</a>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">Главная страница админ панели</a>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Тип</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->type }}</td>
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
                <tr>
                    <td colspan="5">Товаров пока нет.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection