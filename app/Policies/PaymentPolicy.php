<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Payment $payment): bool
    {
        return Payment::query()->whereKey($payment->getKey())->exists();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isReseller() || $user->isSubReseller();
    }
}
