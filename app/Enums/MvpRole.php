<?php

namespace App\Enums;

enum MvpRole: string
{
    case SuperAdmin = 'super_admin';
    case Reseller = 'reseller';
    case SubReseller = 'sub_reseller';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Reseller => 'Reseller',
            self::SubReseller => 'Sub Reseller',
            self::Client => 'Client',
        };
    }
}
