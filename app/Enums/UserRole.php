<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'administrador';
    case EMPLOYED = 'empleados';
    case CASHEER = 'cajero';

    public static function getAllowedRoles(): array
    {
        return [self::ADMIN->value, self::EMPLOYED->value];
    }
}
