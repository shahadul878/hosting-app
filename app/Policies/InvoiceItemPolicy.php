<?php

namespace App\Policies;

use App\Models\InvoiceItem;
use App\Models\User;

class InvoiceItemPolicy
{
    public function view(User $user, InvoiceItem $invoiceItem): bool
    {
        return InvoiceItem::query()->whereKey($invoiceItem->getKey())->exists();
    }
}
