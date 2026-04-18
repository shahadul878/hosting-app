<?php

namespace Database\Factories;

use App\Enums\ServiceRequestStatus;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    protected $model = ServiceRequest::class;

    public function definition(): array
    {
        return [
            'client_user_id' => User::factory(),
            'reseller_user_id' => null,
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => ServiceRequestStatus::Queued,
            'metadata' => [],
        ];
    }
}
