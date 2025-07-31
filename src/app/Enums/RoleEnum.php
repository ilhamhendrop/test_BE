<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case VERIFIKATOR = 'verifikator';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'admin',
            self::VERIFIKATOR => 'verifikator',
            self::USER => 'user',
        };
    }
}
