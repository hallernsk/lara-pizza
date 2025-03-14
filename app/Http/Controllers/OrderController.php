<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; //Для транзакций

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the orders.
     */
    public function index(): JsonResponse
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get(); // Получаем заказы текущего пользователя
        return response()->json($orders);
    }

    /**
     * Display the specified order.
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::findOrFail($id); 
        if($order->user_id != Auth::id() ){
          abort(403); //Если заказ не принадлежит текущему пользователю
        }
        return response()->json($order);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart?->items()->exists()) {
            return response()->json(['message' => 'Cart is empty!'], 400);
        }

        try {
            $order = $this->orderService->createOrderFromCart(
                $cart, 
                $request->validated()
            );

            return response()->json([
                'message' => 'Заказ успешно создан!',
                'order' => $order
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при создании заказа.'
            ], 500);
        }
    }
}