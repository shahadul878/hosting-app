<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ResellerProductPrice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResellerProductPrice>
 */
class ResellerProductPriceFactory extends Factory
{
    protected $model = ResellerProductPrice::class;

    public function definition(): array
    {
        $custom = fake()->randomFloat(2, 10, 200);
        $min = fake()->randomFloat(2, 1, min(50, (float) $custom));

        return [
            'reseller_user_id' => User::factory(),
            'product_id' => Product::factory(),
            'custom_price' => $custom,
            'minimum_price' => $min,
        ];
    }
}
