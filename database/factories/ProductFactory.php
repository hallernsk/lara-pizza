<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Product::class; //Указываем явно модель
     public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(3), // Уникальное название из 3 слов
            'description' => fake()->paragraph(), // Описание - случайный абзац
            'price' => fake()->randomFloat(2, 5, 50), // Случайная цена от 5 до 50 (2 знака после запятой)
            'image' => null, // без изображений
            'type' => fake()->randomElement(['pizza', 'drink']), // Случайный тип: pizza или drink
        ];
    }
}
