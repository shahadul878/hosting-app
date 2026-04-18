<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return Invoice::query()->whereKey($invoice->getKey())->exists();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isReseller() || $user->isSubReseller();
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $this->view($user, $invoice);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->isSuperAdmin();
    }
}
