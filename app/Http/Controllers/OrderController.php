<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB; //Для транзакций

class OrderController extends Controller
{
    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'delivery_time' => 'nullable|date',
        ]);

       //Получаем данные из сессии
        $cart = Cart::where('user_id', Auth::id())->first();
        if (!$cart || $cart->items->isEmpty()) {
          return redirect()->route('cart.index')->withErrors('Ваша корзина пуста!');
        }

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
            return redirect()->route('orders.index')->with('success', 'Заказ успешно создан!');
        }
        catch (\Exception $e){
            DB::rollBack(); //Откатываем транзакцию
            // Обработка ошибок, например, логирование
            return back()->withErrors('Ошибка при создании заказа.');
        }
    }

    /**
     * Display a listing of the orders.
     */
    public function index(): View
    {
        $orders = Order::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get(); // Получаем заказы текущего пользователя
        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(int $id): View
    {
        $order = Order::findOrFail($id); //findOrFail - вызовет исключение, если не найдено
        if($order->user_id != Auth::id() ){
          abort(403); //Если заказ не принадлежит текущему пользователю
        }
        return view('orders.show', compact('order'));
    }
}