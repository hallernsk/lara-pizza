<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user; 
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }


    public function test_user_can_get_cart()
    {
        $product = Product::factory()->create(['type' => 'drink']);
        $cart = Cart::create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/cart');

        $response->assertStatus(200);
    }

    public function test_user_can_add_product_to_cart()
    {
        $product = Product::factory()->create(['type' => 'pizza']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Товар добавлен в корзину']);
    }

    public function test_user_can_remove_product_from_cart()
    {
        $product = Product::factory()->create();
        $cart = Cart::create(['user_id' => $this->user->id]);
        $cartItem = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/cart/{$product->id}');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Товар удалён из корзины.']);
    }

    public function test_user_can_update_product_quantity()
    {
        $product = Product::factory()->create(['type' => 'drink']);
        $cart = Cart::create(['user_id' => $this->user->id]);
        $cartItem = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patchJson("/api/cart/{$product->id}", [
            'quantity' => 7
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Количество товара обновлено']);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 7
        ]);
    }
}
