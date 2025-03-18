<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $adminToken;
    protected User $user;
    protected string $userToken;
    protected Collection $orders;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем администратора
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->adminToken = JWTAuth::fromUser($this->admin);

        // Создаем обычного пользователя
        $this->user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($this->user);

        // Создаем тестовые заказы
        $this->orders = Order::factory()
            ->count(3)
            ->create()
            ->each(function ($order) {
                $order->items()->create([
                    'product_id' => Product::factory()->create()->id,
                    'quantity' => 2,
                    'price' => 10.99
                ]);
            });
    }

    public function test_admin_can_get_all_orders(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/admin/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3);   // ответ содержит 3 заказа (столько мы создали)
    }

    public function test_non_admin_cannot_get_all_orders(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken,
        ])->getJson('/api/admin/orders');

        $response->assertStatus(403);
    }

    public function test_admin_can_get_one_order(): void
    {
        $order = $this->orders->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson("/api/admin/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $order->id,
                'status' => $order->status
            ]);
    }

    public function test_admin_can_update_order_status(): void
    {
        $order = $this->orders->first();
        $newStatus = 'processing';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->putJson("/api/admin/orders/{$order->id}", [
            'status' => $newStatus
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Статус заказа обновлён',
                'order' => [
                    'id' => $order->id,
                    'status' => $newStatus
                ]
            ]);    
    }

    public function test_non_admin_cannot_update_order_status(): void
    {
        $order = $this->orders->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken,
        ])->putJson("/api/admin/orders/{$order->id}", [
            'status' => 'processing'
        ]);

        $response->assertStatus(403);
    }
}
