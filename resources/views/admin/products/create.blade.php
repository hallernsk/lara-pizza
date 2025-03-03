{{-- resources/views/admin/products/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Создание нового товара</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Название:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Описание:</label>
                <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Цена:</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price') }}" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Изображение:</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Тип:</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="pizza" {{ old('type') == 'pizza' ? 'selected' : '' }}>Пицца</option>
                    <option value="drink" {{ old('type') == 'drink' ? 'selected' : '' }}>Напиток</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Создать товар</button>
        </form>
    </div>
@endsection