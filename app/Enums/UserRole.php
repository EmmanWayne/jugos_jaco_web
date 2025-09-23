<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERADMIN = 'superadministrador';
    case ADMIN = 'administrador';
    case EMPLOYED = 'empleados';
    case CASHEER = 'cajero';

    public static function getAllowedRoles(): array
    {
        return [self::SUPERADMIN->value, self::ADMIN->value, self::EMPLOYED->value];
    }
}
