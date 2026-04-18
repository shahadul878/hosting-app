<?php

namespace App\Models\Scopes;

use App\Models\User;
use App\Support\HostingAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VisibleDomainsScope implements Scope
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

        $builder->whereHas('service', function (Builder $q) use ($user): void {
            HostingAccess::constrainOwnedService($q, $user);
        });
    }
}
