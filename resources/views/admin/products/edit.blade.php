{{-- resources/views/admin/products/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Редактирование товара: {{ $product->name }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- !!! Важно: метод PUT/PATCH для обновления --}}

            <div class="mb-3">
                <label for="name" class="form-label">Название:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Описание:</label>
                <textarea class="form-control" id="description" name="description">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Цена:</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Изображение:</label>
                <input type="file" class="form-control" id="image" name="image">
                @if ($product->image)
                    <p>Текущее изображение:</p>
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 200px;">
                @endif
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Тип:</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="pizza" {{ old('type', $product->type) == 'pizza' ? 'selected' : '' }}>Пицца</option>
                    <option value="drink" {{ old('type', $product->type) == 'drink' ? 'selected' : '' }}>Напиток</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>
        <br>
         <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Назад к списку</a>
    </div>
@endsection