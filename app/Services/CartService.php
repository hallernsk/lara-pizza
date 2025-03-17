<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartService
{
    public function addProductToCart($productId, $quantity)
    {
        // Получаем или создаем корзину для текущего пользователя
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        // Находим продукт или выбрасываем исключение, если он не найден
        $product = Product::findOrFail($productId);

        // Проверяем, есть ли уже такой товар в корзине
        $cartItem = $cart->items()->where('product_id', $productId)->first();

        // Валидация лимитов
        $pizzaCount = 0;
        $drinkCount = 0;

        foreach ($cart->items as $item) {
            if ($item->product->type === 'pizza') {
                $pizzaCount += $item->quantity;
            } else {
                $drinkCount += $item->quantity;
            }
        }

        if ($product->type === 'pizza' && ($pizzaCount + $quantity) > 10) {
            throw new \Exception('Нельзя добавить больше 10 пицц!!!!!');
        }

        if ($product->type === 'drink' && ($drinkCount + $quantity) > 20) {
            throw new \Exception('Нельзя добавить больше 20 напитков!!!!!');
        }

        // Если товар уже есть, увеличиваем количество
        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity,
            ]);
        } else {
            // Если товара нет, создаём новый элемент корзины
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return $cart;
    }

    public function updateProductQuantity($productId, $quantity)
    {
        // Получаем корзину текущего пользователя
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            throw new \Exception('Корзина не найдена.');
        }

        // Находим продукт или выбрасываем исключение, если он не найден
        $product = Product::findOrFail($productId);

        // Проверяем лимиты
        $pizzaCount = 0;
        $drinkCount = 0;

        foreach ($cart->items as $item) {
            if ($item->product->type === 'pizza') {
                $pizzaCount += ($item->product_id == $productId) ? $quantity : $item->quantity;
            } else {
                $drinkCount += ($item->product_id == $productId) ? $quantity : $item->quantity;
            }
        }

        if ($product->type === 'pizza' && $pizzaCount > 10) {
            throw new \Exception('Нельзя добавить больше 10 пицц.');
        }

        if ($product->type === 'drink' && $drinkCount > 20) {
            throw new \Exception('Нельзя добавить больше 20 напитков.');
        }

        // Обновляем количество товара в корзине
        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if (!$cartItem) {
            throw new \Exception('Товар не найден в корзине.');
        }

        $cartItem->update(['quantity' => $quantity]);

        return $cart;
    }

}