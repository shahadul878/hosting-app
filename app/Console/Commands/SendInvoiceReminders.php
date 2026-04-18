<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Notifications\InvoiceReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendInvoiceReminders extends Command
{
    protected $signature = 'hosting:send-invoice-reminders';

    protected $description = 'Send reminder emails for invoices approaching their due date';

    public function handle(): int
    {
        $daysList = config('hosting.invoice_reminder_days_before_due', []);
        $sent = 0;
        $today = now()->toDateString();

        foreach ($daysList as $days) {
            $due = now()->addDays((int) $days)->toDateString();

            Invoice::withoutGlobalScopes()
                ->with('client')
                ->whereDate('due_date', $due)
                ->whereIn('status', [InvoiceStatus::Sent, InvoiceStatus::PartiallyPaid, InvoiceStatus::Overdue])
                ->cursor()
                ->each(function (Invoice $invoice) use (&$sent, $days, $today): void {
                    $cacheKey = "hosting:invoice-reminder:{$invoice->id}:{$days}:{$today}";
                    if (Cache::has($cacheKey)) {
                        return;
                    }

                    $invoice->client?->notify(new InvoiceReminderNotification($invoice, (int) $days));
                    Cache::put($cacheKey, true, now()->addDay());
                    $sent++;
                });
        }

        $this->info("Queued {$sent} invoice reminder(s).");

        return self::SUCCESS;
    }
}
