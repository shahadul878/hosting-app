<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invoice $invoice, public int $daysUntilDue) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invoice '.$this->invoice->invoice_number.' reminder')
            ->line('This is a reminder that invoice '.$this->invoice->invoice_number.' is due in '.$this->daysUntilDue.' day(s).')
            ->line('Amount due: '.$this->invoice->total);
    }
}
