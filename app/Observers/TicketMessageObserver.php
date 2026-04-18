<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\TicketReplyNotification;

class TicketMessageObserver
{
    public function created(TicketMessage $message): void
    {
        if ($message->is_internal) {
            return;
        }

        $ticket = Ticket::withoutGlobalScopes()->find($message->ticket_id);
        $client = $ticket ? User::query()->find($ticket->client_user_id) : null;

        if ($client === null) {
            return;
        }

        if ((int) $message->user_id === (int) $client->id) {
            return;
        }

        $messageModel = $message->fresh();
        $client->notify(new TicketReplyNotification($ticket, $messageModel));
    }
}
