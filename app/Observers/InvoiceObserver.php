<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\User;
use App\Notifications\InvoiceCreatedNotification;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        $client = User::query()->find($invoice->client_user_id);
        $client?->notify(new InvoiceCreatedNotification($invoice->fresh()));
    }
}
