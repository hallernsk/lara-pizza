<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB; //Для транзакций

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(): JsonResponse
    {
        // dd(Auth::id()); // ID текущего пользователя
        $orders = Order::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get(); // Получаем заказы текущего пользователя
        // return view('orders.index', compact('orders'));
        return response()->json($orders);
    }

    /**
     * Display the specified order.
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::findOrFail($id); //findOrFail - вызовет исключение, если не найдено
        if($order->user_id != Auth::id() ){
          abort(403); //Если заказ не принадлежит текущему пользователю
        }
        // return view('orders.show', compact('order'));
        return response()->json($order);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'delivery_time' => 'nullable|date',
        ]);

        // dd('test order.store-1');

        $cart = Cart::where('user_id', Auth::id())->first();
        // dd($cart);  // null
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty!',
             ]); 
        }
        // dd('test order.store-2');

        DB::beginTransaction(); //Начинаем транзакцию
        try {
            $totalPrice = 0;
             foreach ($cart->items as $item) {
                $totalPrice += $item->product->price * $item->quantity;
            }

            // 1. Создаём новый заказ
            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'pending', // Начальный статус
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'address' => $request->input('address'),
                'delivery_time' => $request->input('delivery_time'),
                'total_price' => $totalPrice, // Считаем общую стоимость
            ]);

            // 2. Копируем данные из корзины в заказ
            foreach ($cart->items as $item) {
                 $product = Product::find($item->product_id); //Получаем актуальную цену
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $product->price, // !!! Сохраняем цену на момент заказа
                ]);
            }

            // 3. Очищаем корзину
            $cart->items()->delete(); // Удаляем все элементы корзины
            $cart->delete(); //Удаляем саму корзину

            DB::commit(); //Фиксируем транзакцию
            return response()->json([
                'message' => 'Заказ успешно создан!'
             ]);


        }
        catch (\Exception $e){
            DB::rollBack(); //Откатываем транзакцию
            // Обработка ошибок
            return response()->json([
                'message' => 'Ошибка при создании заказа.'
             ]);
        }
    }
}