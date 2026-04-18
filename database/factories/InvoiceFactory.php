<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 20, 800);
        $tax = round($subtotal * 0.1, 2);

        return [
            'invoice_number' => 'INV-'.fake()->unique()->numerify('######'),
            'client_user_id' => User::factory(),
            'reseller_user_id' => null,
            'service_id' => null,
            'status' => InvoiceStatus::Draft,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total' => $subtotal + $tax,
            'due_date' => now()->addDays(14)->toDateString(),
            'paid_at' => null,
            'notes' => null,
        ];
    }
}
