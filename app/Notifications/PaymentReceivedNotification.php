<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->payment->loadMissing('invoice');

        $number = $this->payment->invoice?->invoice_number ?? 'unknown';

        return (new MailMessage)
            ->subject('Payment received')
            ->line('We recorded a payment of '.$this->payment->amount.' for invoice '.$number.'.')
            ->line('Thank you.');
    }
}
