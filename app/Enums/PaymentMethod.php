<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Card = 'card';
    case BankTransfer = 'bank_transfer';
    case Cash = 'cash';
    case Credit = 'credit';
    case Other = 'other';
}
