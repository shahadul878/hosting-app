<?php

namespace App\Models\Scopes;

use App\Models\User;
use App\Support\HostingAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VisibleTicketMessagesScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! auth()->check()) {
            $builder->whereRaw('0 = 1');

            return;
        }

        /** @var User $user */
        $user = auth()->user();
        if (HostingAccess::userMayBypass($user)) {
            return;
        }

        $builder->whereHas('ticket', function (Builder $q) use ($user): void {
            HostingAccess::constrainTicket($q, $user);
        });
    }
}
