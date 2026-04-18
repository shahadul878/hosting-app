<?php

namespace App\Http\Requests;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Invoice::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'client_user_id' => ['required', 'exists:users,id'],
            'reseller_user_id' => ['nullable', 'exists:users,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'due_date' => ['required', 'date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['sometimes', Rule::enum(InvoiceStatus::class)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.service_id' => ['nullable', 'exists:services,id'],
            'expected_total' => ['nullable', 'numeric'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $items = $this->input('items', []);
            $subtotal = 0.0;
            foreach ($items as $line) {
                $subtotal += ((int) ($line['quantity'] ?? 0)) * ((float) ($line['unit_price'] ?? 0));
            }
            $subtotal = round($subtotal, 2);
            $tax = round((float) ($this->input('tax_amount', 0)), 2);
            $computed = round($subtotal + $tax, 2);

            if ($this->filled('expected_total')) {
                $expected = round((float) $this->input('expected_total'), 2);
                if (abs($computed - $expected) > 0.02) {
                    $validator->errors()->add('expected_total', 'Totals do not match line items and tax.');
                }
            }
        });
    }
}
