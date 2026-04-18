<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Notifications\PaymentReceivedNotification;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        $invoice = Invoice::withoutGlobalScopes()
            ->with('client')
            ->find($payment->invoice_id);

        $client = $invoice?->client;
        if ($client === null) {
            return;
        }

        $payment->setRelation('invoice', $invoice);
        $client->notify(new PaymentReceivedNotification($payment));
    }
}
