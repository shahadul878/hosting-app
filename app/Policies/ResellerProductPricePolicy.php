<?php

namespace App\Policies;

use App\Models\ResellerProductPrice;
use App\Models\User;

class ResellerProductPricePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isReseller() || $user->isSubReseller();
    }

    public function view(User $user, ResellerProductPrice $resellerProductPrice): bool
    {
        return ResellerProductPrice::query()->whereKey($resellerProductPrice->getKey())->exists();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isReseller();
    }

    public function update(User $user, ResellerProductPrice $resellerProductPrice): bool
    {
        return $this->view($user, $resellerProductPrice);
    }

    public function delete(User $user, ResellerProductPrice $resellerProductPrice): bool
    {
        return $this->view($user, $resellerProductPrice);
    }
}
