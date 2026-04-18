<?php

namespace App\Policies;

use App\Models\Domain;
use App\Models\User;

class DomainPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Domain $domain): bool
    {
        return Domain::query()->whereKey($domain->getKey())->exists();
    }

    public function update(User $user, Domain $domain): bool
    {
        return $this->view($user, $domain);
    }
}
