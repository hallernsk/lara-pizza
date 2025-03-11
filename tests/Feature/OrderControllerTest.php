<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;
    protected $cart;
    protected $products;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
        
        // Создаем корзину с товарами
        $this->cart = Cart::factory()->create(['user_id' => $this->user->id]);
        $this->products = Product::factory()->count(2)->create();
        
        foreach ($this->products as $product) {
            CartItem::factory()->create([
                'cart_id' => $this->cart->id,
                'product_id' => $product->id,
                'quantity' => 2
            ]);
        }
    }

    public function test_user_can_get_their_orders()
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id', 
                    'status',
                    'total_price',
                    'created_at'
                ]
            ]);
    }

    public function test_user_can_view_their_one_order()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $order->id,
                'user_id' => $this->user->id
            ]);
    }

    public function test_user_can_create_order_from_cart()
    {
        $requestData = [
            'phone' => '1234567890',
            'email' => 'test@example.com',
            'address' => 'Test Address',
            'delivery_time' => now()->addDay()->toDateTimeString()
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/orders', $requestData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Заказ успешно создан!']);
    }

}
