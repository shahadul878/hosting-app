<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'client_user_id' => User::factory(),
            'opened_by_user_id' => fn (array $attributes) => $attributes['client_user_id'],
            'assigned_to_user_id' => null,
            'subject' => fake()->sentence(4),
            'status' => TicketStatus::Open,
            'priority' => TicketPriority::Normal,
        ];
    }
}
