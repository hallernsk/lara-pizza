<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddProductToCartRequest;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * Display the cart.
     */
    public function show(): JsonResponse
    {
        $cart = null; // Инициализируем переменную

        if (Auth::check()) {
            // Если пользователь авторизован, получаем его корзину
            $cart = Cart::with('items.product')->where('user_id', Auth::id())->first(); 
        }
        else {
            //для неавторизованных
            $cart = null; 
        }
        return response()->json($cart);
    }
    
    /**
     * Add a product to the cart.
     */
    public function add(AddProductToCartRequest $request): JsonResponse
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        try {
            $this->cartService->addProductToCart($productId, $quantity);
            return response()->json(['message' => 'Товар добавлен в корзину'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $productId): JsonResponse
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            $cart->items()->where('product_id', $productId)->delete();
            return response()->json(['message' => 'Товар удалён из корзины.'], 200);
        }
    
        return response()->json(['message' => 'Корзина не найдена.'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $productId): JsonResponse 
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $request->input('quantity');

        try {
            $this->cartService->updateProductQuantity($productId, $quantity);
            return response()->json(['message' => 'Количество товара обновлено'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }    
    }
}