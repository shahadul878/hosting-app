<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceBuilder
{
    public function generateInvoiceNumber(): string
    {
        do {
            $candidate = 'INV-'.strtoupper(Str::random(10));
        } while (Invoice::withoutGlobalScopes()->where('invoice_number', $candidate)->exists());

        return $candidate;
    }

    /**
     * @param  array<int, array{description: string, quantity: int|float, unit_price: string|float, product_id?: int|null, service_id?: int|null}>  $lines
     */
    public function createDraftInvoice(array $attributes, array $lines): Invoice
    {
        if ($lines === []) {
            throw new \InvalidArgumentException('Invoice must contain at least one line item.');
        }

        return DB::transaction(function () use ($attributes, $lines) {
            $subtotal = 0.0;
            $normalized = [];
            foreach ($lines as $line) {
                $qty = (int) $line['quantity'];
                $unit = (float) $line['unit_price'];
                $lineTotal = round($qty * $unit, 2);
                $subtotal += $lineTotal;
                $normalized[] = [
                    'description' => $line['description'],
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'line_total' => $lineTotal,
                    'product_id' => $line['product_id'] ?? null,
                    'service_id' => $line['service_id'] ?? null,
                ];
            }

            $subtotal = round($subtotal, 2);
            $taxAmount = round((float) ($attributes['tax_amount'] ?? 0), 2);
            $total = round($subtotal + $taxAmount, 2);

            $invoice = Invoice::withoutGlobalScopes()->create([
                'invoice_number' => $attributes['invoice_number'] ?? $this->generateInvoiceNumber(),
                'client_user_id' => $attributes['client_user_id'],
                'reseller_user_id' => $attributes['reseller_user_id'] ?? null,
                'service_id' => $attributes['service_id'] ?? null,
                'status' => $attributes['status'] ?? InvoiceStatus::Draft,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'due_date' => $attributes['due_date'],
                'paid_at' => $attributes['paid_at'] ?? null,
                'notes' => $attributes['notes'] ?? null,
            ]);

            foreach ($normalized as $row) {
                InvoiceItem::withoutGlobalScopes()->create([
                    'invoice_id' => $invoice->id,
                    'description' => $row['description'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'line_total' => $row['line_total'],
                    'product_id' => $row['product_id'],
                    'service_id' => $row['service_id'],
                ]);
            }

            return $invoice->fresh(['items']);
        });
    }

    public function recalculateTotals(Invoice $invoice): void
    {
        $subtotal = round((float) $invoice->items()->withoutGlobalScopes()->sum('line_total'), 2);
        $tax = round((float) $invoice->tax_amount, 2);
        $invoice->forceFill([
            'subtotal' => $subtotal,
            'total' => round($subtotal + $tax, 2),
        ])->save();
    }
}
