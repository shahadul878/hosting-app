<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Service $service): bool
    {
        return Service::query()->whereKey($service->getKey())->exists();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isReseller() || $user->isSubReseller();
    }

    public function update(User $user, Service $service): bool
    {
        return $this->view($user, $service);
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->isSuperAdmin() || $this->view($user, $service);
    }
}
