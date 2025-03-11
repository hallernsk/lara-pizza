<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(): JsonResponse
    {
        if (!Auth::user()->is_admin) {  // Проверка на админа
            abort(403, 'Unauthorized action.'); //
         }
          $orders = Order::with('user', 'items.product')->orderBy('created_at', 'desc')->get(); // Eager loading + сортировка
          return response()->json($orders);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        return response()->json($order);
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,delivering,completed,canceled',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Статус заказа обновлён',
                                 'order' => $order]);
    }
}