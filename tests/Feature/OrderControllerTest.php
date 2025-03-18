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

    protected User $user;
    protected User $user2;
    protected string $token;
    protected string $token2;
    protected Cart $cart;
    protected Collection $products;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->user2 = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
        $this->token2 = JWTAuth::fromUser($this->user2);
        
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

    public function test_user_can_get_their_orders(): void
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
 
    public function test_user_can_get_their_one_order(): void
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

    public function test_user_cannot_get_non_their_one_order(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user2->id]);

        // Пытаемся получить заказ другого пользователя
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/orders/{$order->id}");

        $response->assertStatus(403);    
    }

    public function test_user_can_create_order_from_cart(): void
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

        $response->assertStatus(201)
            ->assertJson(['message' => 'Заказ успешно создан!']);
    }

    public function test_order_creation_without_phone(): void
    {
        $requestData = [
            'email' => 'test@example.com',
            'address' => 'Test Address',
            'delivery_time' => now()->addDay()->toDateTimeString()
        ];
        $response = $this->withToken($this->token)->postJson('/api/orders', $requestData);
    
        $response->assertJsonValidationErrors(['phone']);
    }

    public function test_order_creation_without_email(): void
    {
        $requestData = [
            'phone' => '1234567890',
            'address' => 'Test Address',
            'delivery_time' => now()->addDay()->toDateTimeString()
        ];
        $response = $this->withToken($this->token)->postJson('/api/orders', $requestData);
    
        $response->assertJsonValidationErrors(['email']);
    }
}
