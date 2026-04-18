<?php

namespace App\Notifications;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DomainExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Domain $domain, public int $daysRemaining) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Domain expiry notice: '.$this->domain->domain_name)
            ->line('The domain '.$this->domain->domain_name.' expires in '.$this->daysRemaining.' day(s).')
            ->line('Expiry date: '.$this->domain->expires_on?->toFormattedDateString());
    }
}
