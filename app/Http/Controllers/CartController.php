<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Add a product to the cart.
     */
    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:100', //При необходимости, добавить валидацию
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1); // Количество, по умолчанию 1
        $product = Product::findOrFail($productId);

        // 1. Получаем корзину пользователя (или создаём новую)
        // $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

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
            return back()->withErrors(['message' => 'Нельзя добавить больше 10 пицц']);
        }
        if ($product->type === 'drink' && ($drinkCount + $quantity) > 20) {
            return back()->withErrors(['message' => 'Нельзя добавить больше 20 напитков']);
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

        return back()->with('success', 'Товар добавлен в корзину!');
    }
      /**
     * Display the cart.
     */
    public function index(): View
    {
        $cart = null; // Инициализируем переменную

        if (Auth::check()) {
           // Если пользователь авторизован, получаем его корзину
        //   $cart = Cart::where('user_id', auth()->id())->first();
          $cart = Cart::where('user_id', Auth::id())->first();
        }
        else {
          //Временно для неавторизованных
           $cart = null;
        }

        return view('cart.index', compact('cart'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
    
        $productId = $request->input('product_id');
    
        // 1. Получаем корзину пользователя
        // $cart = Cart::where('user_id', auth()->id())->first();
        $cart = Cart::where('user_id', Auth::id())->first();
    
        // 2. Если корзина есть, удаляем элемент
        if ($cart) {
            $cart->items()->where('product_id', $productId)->delete();
        }
    
        return back()->with('success', 'Товар удалён из корзины.');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
    
        // $cart = Cart::where('user_id', auth()->id())->first();
        $cart = Cart::where('user_id', Auth::id())->first();
    
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
              return back()->withErrors(['message' => 'Нельзя добавить больше 10 пицц']);
           }
            if ($product->type === 'drink' && $drinkCount > 20) {
                return back()->withErrors(['message' => 'Нельзя добавить больше 20 напитков']);
           }
            $cart->items()->where('product_id', $productId)->update(['quantity' => $quantity]);
        }
    
        return back()->with('success', 'Количество товара обновлено.');
    }

}