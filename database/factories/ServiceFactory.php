<?php

namespace Database\Factories;

use App\Enums\ServiceStatus;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'client_user_id' => User::factory(),
            'reseller_user_id' => null,
            'sub_reseller_user_id' => null,
            'status' => ServiceStatus::Active,
            'metadata' => [],
            'billing_started_on' => now()->toDateString(),
            'renews_on' => now()->addYear()->toDateString(),
            'next_invoice_on' => now()->addMonth()->toDateString(),
            'cancelled_at' => null,
        ];
    }
}
