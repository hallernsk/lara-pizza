<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CartController extends Controller
{
    /**
     * Display the cart.
     */
    public function index(): JsonResponse
    {
       $cart = null; // Инициализируем переменную

       if (Auth::check()) {
          // Если пользователь авторизован, получаем его корзину
         $cart = Cart::with('items.product')->where('user_id', Auth::id())->first(); //Eager Loading
       }
       else {
          //Временно для неавторизованных
          $cart = null; //Позже сменить на сессии
       }
       return response()->json($cart);
    }
    
    /**
     * Add a product to the cart.
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:100', //При необходимости, добавить валидацию
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1); // Количество, по умолчанию 1
        $product = Product::findOrFail($productId);

        // 1. Получаем корзину пользователя (или создаём новую, если её нет)
        $cart = null;
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        }
        else{
            //пока функционал неавторизованных пользователей не делаем
            return response()->json(['message' => 'Authentication required'], 401);
        }

        // 2. Проверяем, есть ли уже такой товар в корзине
        $cartItem = $cart->items()->where('product_id', $productId)->first();

        //Валидация
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
            return response()->json(['message' => 'Нельзя добавить больше 10 пицц!'], 400);
        }
        if ($product->type === 'drink' && ($drinkCount + $quantity) > 20) {
          return response()->json(['message' => 'Нельзя добавить больше 20 напитков!'], 400);
        }
        //

        if ($cartItem) {
            // 3a. Если товар уже есть, увеличиваем количество
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity,
            ]);
        } else {
            // 3b. Если товара нет, создаём новый элемент корзины
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return response()->json(['message' => 'Товар добавлен в корзину!'], 200); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->input('product_id');

        // 1. Получаем корзину пользователя
         $cart = null;
        if (Auth::check()) {
           // Если пользователь авторизован, получаем его корзину
            $cart = Cart::where('user_id', Auth::id())->first();
        }
        else{
          //пока функционал неавторизованных не делаем
          return response()->json(['message' => 'Authentication required'], 401);
        }

        // 2. Если корзина есть, удаляем элемент
        if ($cart) {
            $cart->items()->where('product_id', $productId)->delete();
             return response()->json(['message' => 'Товар удалён из корзины.'], 200);
        }
        return response()->json(['message' => 'Корзина не найдена.'], 404);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        // 1. Получаем корзину пользователя
         $cart = null;
        if (Auth::check()) {
           // Если пользователь авторизован, получаем его корзину
          $cart = Cart::where('user_id', Auth::id())->first();
        }
        else{
         //пока функционал неавторизованных не делаем
          return response()->json(['message' => 'Authentication required'], 401); // 401 Unauthorized
        }

        if ($cart) {
             //Проверяем лимиты
            $product = Product::findOrFail($productId);
            $pizzaCount = 0;
            $drinkCount = 0;

            foreach ($cart->items as $item) {
               if ($item->product->type === 'pizza') {
                  $pizzaCount += ($item->product_id == $productId) ? $quantity : $item->quantity;
               }
               else {
                 $drinkCount += ($item->product_id == $productId) ? $quantity : $item->quantity;
               }
             }

            if ($product->type === 'pizza' && $pizzaCount > 10) {
               return response()->json(['message' => 'Нельзя добавить больше 10 пицц'], 400);
            }
            if ($product->type === 'drink' && $drinkCount > 20) {
              return response()->json(['message' => 'Нельзя добавить больше 20 напитков'], 400);
            }
            $cart->items()->where('product_id', $productId)->update(['quantity' => $quantity]);
            return response()->json(['message' => 'Количество обновлено'], 200);
        }
        return response()->json(['message' => 'Корзина не найдена.'], 404);
    }
}