<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case EMPLOYED = 'employed';

    public static function getAllowedRoles(): array
    {
        return [self::ADMIN->value, self::EMPLOYED->value];
    }
}
