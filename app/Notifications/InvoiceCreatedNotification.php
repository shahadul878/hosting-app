<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New invoice '.$this->invoice->invoice_number)
            ->line('An invoice has been created for your account.')
            ->line('Total: '.$this->invoice->total)
            ->line('Due: '.$this->invoice->due_date->toFormattedDateString())
            ->line('Thank you for using our services.');
    }
}
