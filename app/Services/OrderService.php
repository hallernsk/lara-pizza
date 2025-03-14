<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrderFromCart(Cart $cart, array $orderData): Order
    {
        return DB::transaction(function () use ($cart, $orderData) {
            // 1. Создаем заказ
            $order = Order::create([
                'user_id' => $cart->user_id,
                'status' => 'pending',
                'total_price' => $this->calculateTotalPrice($cart),
                ...$orderData
            ]);

            // 2. Добавляем товары из корзины
            $this->createOrderItems($order, $cart);

            // 3. Очищаем корзину
            $this->clearCart($cart);

            return $order->load('items');
        });
    }

    private function calculateTotalPrice(Cart $cart): float
    {
        return $cart->items->sum(
            fn($item) => $item->product->price * $item->quantity
        );
    }

    private function createOrderItems(Order $order, Cart $cart): void
    {
        $cart->items->each(function ($item) use ($order) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        });
    }

    private function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->delete();
    }
}