<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 3);
        $unit = fake()->randomFloat(2, 5, 200);
        $line = round($qty * $unit, 2);

        return [
            'invoice_id' => Invoice::factory(),
            'description' => fake()->sentence(4),
            'quantity' => $qty,
            'unit_price' => $unit,
            'line_total' => $line,
            'product_id' => null,
            'service_id' => null,
        ];
    }
}
