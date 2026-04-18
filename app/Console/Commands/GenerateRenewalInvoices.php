<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Enums\ServiceStatus;
use App\Models\Invoice;
use App\Models\Service;
use App\Services\InvoiceBuilder;
use App\Services\PricingResolver;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRenewalInvoices extends Command
{
    protected $signature = 'hosting:generate-renewal-invoices {--date= : Reference date (Y-m-d), default today}';

    protected $description = 'Create draft renewal invoices for services due billing (idempotent per service per month)';

    public function handle(InvoiceBuilder $invoices, PricingResolver $pricing): int
    {
        $date = Carbon::parse($this->option('date') ?? now()->toDateString())->startOfDay();

        $query = Service::withoutGlobalScopes()
            ->with(['product', 'client', 'reseller', 'subReseller'])
            ->where('status', ServiceStatus::Active)
            ->whereNotNull('next_invoice_on')
            ->whereDate('next_invoice_on', '<=', $date);

        $count = 0;
        foreach ($query->cursor() as $service) {
            $periodKey = $service->next_invoice_on?->format('Y-m') ?? $date->format('Y-m');
            $exists = Invoice::withoutGlobalScopes()
                ->where('service_id', $service->id)
                ->whereIn('status', [InvoiceStatus::Draft, InvoiceStatus::Sent, InvoiceStatus::PartiallyPaid])
                ->whereYear('created_at', (int) substr($periodKey, 0, 4))
                ->whereMonth('created_at', (int) substr($periodKey, 5, 2))
                ->exists();

            if ($exists) {
                continue;
            }

            $seller = $service->reseller ?? $service->subReseller ?? $service->client;
            if ($seller === null) {
                continue;
            }

            $unit = $pricing->resolvePrice($seller, $service->product);
            $invoices->createDraftInvoice(
                [
                    'client_user_id' => $service->client_user_id,
                    'reseller_user_id' => $service->reseller_user_id,
                    'service_id' => $service->id,
                    'due_date' => $date->copy()->addDays(14)->toDateString(),
                    'tax_amount' => 0,
                    'status' => InvoiceStatus::Draft,
                    'notes' => 'Renewal invoice',
                ],
                [[
                    'description' => 'Renewal: '.$service->product->name,
                    'quantity' => 1,
                    'unit_price' => $unit,
                    'product_id' => $service->product_id,
                    'service_id' => $service->id,
                ]]
            );
            $count++;
        }

        $this->info("Generated {$count} renewal invoice(s).");

        return self::SUCCESS;
    }
}
