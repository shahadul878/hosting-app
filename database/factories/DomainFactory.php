<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Domain>
 */
class DomainFactory extends Factory
{
    protected $model = Domain::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'domain_name' => fake()->unique()->domainName(),
            'expires_on' => now()->addYear()->toDateString(),
        ];
    }
}
