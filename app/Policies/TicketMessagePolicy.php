<?php

namespace App\Policies;

use App\Enums\MvpRole;
use App\Models\TicketMessage;
use App\Models\User;

class TicketMessagePolicy
{
    public function view(User $user, TicketMessage $ticketMessage): bool
    {
        if ($ticketMessage->is_internal && $user->mvp_role === MvpRole::Client) {
            return false;
        }

        return TicketMessage::query()->whereKey($ticketMessage->getKey())->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }
}
