<?php

namespace Database\Factories;

use App\Enums\ProductType;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('PRD-####')),
            'name' => fake()->words(3, true).' Plan',
            'description' => fake()->optional()->sentence(),
            'product_type' => fake()->randomElement(ProductType::cases()),
            'base_price' => fake()->randomFloat(2, 5, 500),
            'sale_price' => null,
            'config' => [],
        ];
    }
}
