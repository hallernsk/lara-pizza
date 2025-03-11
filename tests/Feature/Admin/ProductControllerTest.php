<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_get_product_list(): void  // Гость
    {
        $response = $this->getJson('/api/products'); // Или '/api'

        $response->assertStatus(200);  
    }

    public function test_guest_get_access_product_show(): void
    {
        $product = Product::factory()->create();
        $response = $this->getJson('/api/products/'. $product->id);
        $response->assertStatus(200);
    }

    public function test_admin_can_get_products_list()
    {
        Product::factory()->count(5)->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $token = JWTAuth::fromUser($admin); // Генерация токена
    
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/admin/products');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    public function test_admin_get_access_product_show(): void
    {
        $product = Product::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/products/'. $product->id);
        $response->assertStatus(200);
    }

    public function test_admin_can_create_product()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $token = JWTAuth::fromUser($admin); 
        $data = [
            'name' => 'Пепперони',
            'price' => 10.99,
            'type' => 'pizza',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/admin/products', $data);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Товар успешно создан!']);
    }

    public function test_user_cannot_create_product()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user); 
        $data = [
            'name' => 'Пепперони',
            'price' => 10.99,
            'type' => 'pizza',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/admin/products', $data);

        $response->assertStatus(403);
    
    }

    
    public function test_admin_can_update_product()
{
    $admin = User::factory()->create(['is_admin' => true]);
    $token = JWTAuth::fromUser($admin); 
    $product = Product::factory()->create([
        'name' => 'Старое название',
        'description' => 'Описание товара',
        'price' => 9.99,
        'type' => 'pizza',
    ]);
    $data = ['name' => 'Новое название',
             'description' => 'Новое описание',
             'price' => 12.99,
             'type' => 'drink',
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->putJson("/api/admin/products/{$product->id}", $data);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Товар успешно обновлен!']);
}

public function test_user_cannot_update_product()
{
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user); 
    $product = Product::factory()->create([
        'name' => 'Старое название',
        'description' => 'Описание товара',
        'price' => 9.99,
        'type' => 'pizza',
    ]);
    $data = ['name' => 'Новое название',
             'description' => 'Новое описание',
             'price' => 12.99,
             'type' => 'drink',
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->putJson("/api/admin/products/{$product->id}", $data);

    $response->assertStatus(403);
}

public function test_admin_can_delete_product()
{
    $admin = User::factory()->create(['is_admin' => true]);
    $token = JWTAuth::fromUser($admin);

    $product = Product::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->deleteJson("/api/admin/products/{$product->id}");

    $response->assertStatus(200)
             ->assertJson(['message' => 'The product has been successfully removed.']);
}

public function test_user_cannot_delete_product()
{
    $admin = User::factory()->create();
    $token = JWTAuth::fromUser($admin);

    $product = Product::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->deleteJson("/api/admin/products/{$product->id}");

    $response->assertStatus(403);
}

}
