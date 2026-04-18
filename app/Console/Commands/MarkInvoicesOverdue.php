<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Console\Command;

class MarkInvoicesOverdue extends Command
{
    protected $signature = 'hosting:mark-invoices-overdue';

    protected $description = 'Mark unpaid invoices past due date as overdue';

    public function handle(): int
    {
        $updated = Invoice::withoutGlobalScopes()
            ->whereIn('status', [InvoiceStatus::Sent, InvoiceStatus::PartiallyPaid])
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => InvoiceStatus::Overdue]);

        $this->info("Marked {$updated} invoice(s) overdue.");

        return self::SUCCESS;
    }
}
