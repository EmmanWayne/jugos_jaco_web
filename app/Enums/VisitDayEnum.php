<?php

namespace App\Enums;

enum VisitDayEnum: string
{
    case LUNES = 'Lunes';
    case MARTES = 'Martes';
    case MIERCOLES = 'Miércoles';
    case JUEVES = 'Jueves';
    case VIERNES = 'Viernes';
    case SABADO = 'Sábado';

    public static function getAllowedDays(): array
    {
        return [
            self::LUNES->value,
            self::MARTES->value,
            self::MIERCOLES->value,
            self::JUEVES->value,
            self::VIERNES->value,
            self::SABADO->value,
        ];
    }

    public static function toArray(): array
    {
        return collect(self::cases())->pluck('value', 'value')->toArray();
    }
}
